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
use app\models\TaskRelationEntity;
use app\usecases\Task\CreateTaskService;
use Throwable;
use yii\base\Exception;

class CompanyOffersCreatedEffectStrategy extends AbstractEffectStrategy
{
	private const TASK_TITLE_TEMPLATE = 'Внести строение, комп. "%s"';

	private const CLASS_NAMES_MAP = [
		1 => 'A',
		2 => 'B',
		3 => 'C',
		4 => 'D',
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
				$this->createOfferTask($payload, $survey);
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
	private function createOfferTask(array $payload, Survey $survey): void
	{
		$company = $survey->chatMember->company;

		$taskDto = $this->taskBuilderFactory->createEffectBuilder()
		                                    ->setCreatedBy($survey->user)
		                                    ->setTitle($this->getTaskTitle($company))
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
	private function getTaskTitle(Company $company): string
	{
		return sprintf(self::TASK_TITLE_TEMPLATE, $company->getShortName());
	}


	/**
	 * @throws \Exception
	 */
	private function parsePayloadToMessage(array $payload): string
	{
		$address     = ArrayHelper::getValue($payload, 'address');
		$area        = ArrayHelper::getValue($payload, 'area');
		$class       = ArrayHelper::getValue($payload, 'class');
		$isLand      = TypeConverterHelper::toBool(ArrayHelper::getValue($payload, 'is_land', false));
		$description = ArrayHelper::getValue($payload, 'description');

		$parts = [];

		$parts[] = $isLand ? 'Участок' : 'Строение';

		if (!$isLand && !empty($class)) {
			$parts[] = $this->resolveClassName(TypeConverterHelper::toInt($class)) . ' класс';
		}

		if ($area) {
			$parts[] = $area . ' м2';
		}

		if ($address) {
			$parts[] = StringHelper::ucFirst($address);
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

	private function resolveClassName(int $classId): string
	{
		return self::CLASS_NAMES_MAP[$classId];
	}
}