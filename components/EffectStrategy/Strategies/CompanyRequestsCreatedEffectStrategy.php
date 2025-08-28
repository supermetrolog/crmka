<?php

namespace app\components\EffectStrategy\Strategies;

use app\builders\Task\TaskBuilderFactory;
use app\components\EffectStrategy\AbstractEffectStrategy;
use app\dto\Task\LinkTaskRelationEntityDto;
use app\helpers\ArrayHelper;
use app\helpers\StringHelper;
use app\helpers\TypeConverterHelper;
use app\kernel\common\database\interfaces\transaction\TransactionBeginnerInterface;
use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\ChatMemberMessage;
use app\models\Company\Company;
use app\models\QuestionAnswer;
use app\models\Survey;
use app\models\SurveyQuestionAnswer;
use app\models\Task;
use app\models\TaskRelationEntity;
use app\usecases\Task\CreateTaskService;
use Throwable;
use yii\base\Exception;

class CompanyRequestsCreatedEffectStrategy extends AbstractEffectStrategy
{
	private const TASK_TITLE_TEMPLATE = 'Новый запрос (%s) от компании "%s"';
	private const DEAL_TYPES_MAP      = [
		1 => 'Аренда',
		2 => 'Продажа',
		3 => 'Ответ-хранение',
		4 => 'Субаренда',
		5 => 'Строительство',
	];

	private TaskBuilderFactory           $taskBuilderFactory;
	private TransactionBeginnerInterface $transactionBeginner;
	private CreateTaskService            $createTaskService;

	public function __construct(
		TaskBuilderFactory $taskBuilderFactory,
		TransactionBeginnerInterface $transactionBeginner,
		CreateTaskService $createTaskService

	)
	{
		$this->taskBuilderFactory  = $taskBuilderFactory;
		$this->transactionBeginner = $transactionBeginner;
		$this->createTaskService   = $createTaskService;
	}

	/**
	 * @throws Exception
	 * @throws \Exception
	 */
	public function shouldBeProcessed(Survey $survey, QuestionAnswer $answer): bool
	{
		if ($answer->surveyQuestionAnswer->hasAnswer()) {
			$jsonData = $answer->surveyQuestionAnswer->getJSON();

			return ArrayHelper::isArray($jsonData) || ArrayHelper::notEmpty($jsonData);
		}

		return false;
	}

	/**
	 * @throws Exception
	 * @throws Throwable
	 */
	public function process(Survey $survey, SurveyQuestionAnswer $surveyQuestionAnswer, ChatMemberMessage $surveyChatMemberMessage): void
	{
		$jsonData = $surveyQuestionAnswer->getJSON();

		$tx = $this->transactionBeginner->begin();

		try {
			foreach ($jsonData as $payload) {
				$this->createRequestTask($payload, $survey);
			}

			$tx->commit();
		} catch (Throwable $th) {
			$tx->rollback();
			throw $th;
		}
	}

	/**
	 * @throws Throwable
	 * @throws ModelNotFoundException
	 * @throws SaveModelException
	 */
	private function createRequestTask(array $payload, Survey $survey): void
	{
		$company = $survey->chatMember->company;

		$taskDto = $this->taskBuilderFactory->createEffectBuilder()
		                                    ->setType(Task::TYPE_REQUEST_HANDLING)
		                                    ->setCreatedBy($survey->user)
		                                    ->setTitle($this->parsePayloadToTitle($payload, $company))
		                                    ->setMessage($this->parsePayloadToMessage($payload))
		                                    ->build();

		$relationsDtos = [
			$this->makeTaskRelationEntityDto($company),
			$this->makeTaskRelationEntityDto($survey)
		];

		$this->createTaskService->create($taskDto, [], $relationsDtos);
	}

	/**
	 * @throws \Exception
	 */
	private function parsePayloadToTitle(array $payload, Company $company): string
	{
		$dealType     = ArrayHelper::getValue($payload, 'deal_type');
		$dealTypeName = $this->resolveDealTypeName(TypeConverterHelper::toInt($dealType));

		return sprintf(self::TASK_TITLE_TEMPLATE, $dealTypeName, $company->getShortName());
	}


	/**
	 * @throws \Exception
	 */
	private function parsePayloadToMessage(array $payload): string
	{
		$areaMin     = ArrayHelper::getValue($payload, 'area_min');
		$areaMax     = ArrayHelper::getValue($payload, 'area_max');
		$dealType    = ArrayHelper::getValue($payload, 'deal_type');
		$isExpress   = ArrayHelper::getValue($payload, 'express', false);
		$location    = ArrayHelper::getValue($payload, 'location', 'Локация не указана');
		$description = ArrayHelper::getValue($payload, 'description');

		$parts = [];

		if ($dealType) {
			$parts[] = $this->resolveDealTypeName(TypeConverterHelper::toInt($dealType));
		}

		if (TypeConverterHelper::toBool($isExpress)) {
			$parts[] = 'Экспресс';
		}

		if ($areaMin && $areaMax) {
			$parts[] = 'От ' . $areaMin . ' до ' . $areaMax . ' м2';
		} else {
			if ($areaMin) {
				$parts[] = 'От ' . $areaMin . ' м2';
			} else {
				if ($areaMax) {
					$parts[] = 'До ' . $areaMax . ' м2';
				}
			}
		}

		if ($location) {
			$parts[] = StringHelper::ucFirst($location);
		}

		if ($description) {
			$parts[] = $description;
		}

		return StringHelper::join(
			StringHelper::SYMBOL_SPACED_DOT,
			...ArrayHelper::filter($parts, static fn($part) => StringHelper::isString($part) && StringHelper::isNotEmpty($part))
		);
	}

	private function makeTaskRelationEntityDto($entity): LinkTaskRelationEntityDto
	{
		return new LinkTaskRelationEntityDto([
			'entityId'     => $entity->id,
			'entityType'   => $entity::getMorphClass(),
			'relationType' => TaskRelationEntity::RELATION_TYPE_SYSTEM
		]);
	}

	private function resolveDealTypeName(int $dealTypeId): string
	{
		return self::DEAL_TYPES_MAP[$dealTypeId];
	}
}