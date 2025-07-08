<?php

namespace app\components\EffectStrategy\Strategies;

use app\builders\Task\TaskBuilderFactory;
use app\components\EffectStrategy\AbstractEffectStrategy;
use app\components\EffectStrategy\Service\CreateEffectSystemMessageService;
use app\dto\Request\PassiveRequestDto;
use app\dto\Task\LinkTaskRelationEntityDto;
use app\helpers\ArrayHelper;
use app\helpers\TypeConverterHelper;
use app\kernel\common\database\interfaces\transaction\TransactionBeginnerInterface;
use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\ChatMemberMessage;
use app\models\QuestionAnswer;
use app\models\Request;
use app\models\Survey;
use app\models\SurveyQuestionAnswer;
use app\models\Task;
use app\models\TaskRelationEntity;
use app\repositories\RequestRepository;
use app\usecases\ChatMember\ChatMemberService;
use app\usecases\Request\RequestService;
use app\usecases\Task\CreateTaskService;
use Throwable;
use yii\base\ErrorException;
use yii\base\Exception;

class CompanyRequestsChangesEffectStrategy extends AbstractEffectStrategy
{
	private const DISABLE_MESSAGE   = 'Запрос больше не актуален. Переведен в пассив.';
	private const EDIT_TASK_MESSAGE = 'Изменения в запросе #%d (комп. %s)';

	private const ANSWER_REQUEST_MUST_BE_DISABLED = 2;
	private const ANSWER_REQUEST_MUST_BE_EDITED   = 3;

	private TaskBuilderFactory               $taskBuilderFactory;
	private CreateEffectSystemMessageService $effectSystemMessageService;
	private TransactionBeginnerInterface     $transactionBeginner;
	private RequestService                   $requestService;
	private RequestRepository                $requestRepository;
	private ChatMemberService                $chatMemberService;
	private CreateTaskService                $createTaskService;

	public function __construct(
		TaskBuilderFactory $taskBuilderFactory,
		CreateEffectSystemMessageService $effectSystemMessageService,
		TransactionBeginnerInterface $transactionBeginner,
		RequestService $requestService,
		RequestRepository $requestRepository,
		ChatMemberService $chatMemberService,
		CreateTaskService $createTaskService

	)
	{
		$this->taskBuilderFactory         = $taskBuilderFactory;
		$this->effectSystemMessageService = $effectSystemMessageService;
		$this->transactionBeginner        = $transactionBeginner;
		$this->requestService             = $requestService;
		$this->requestRepository          = $requestRepository;
		$this->chatMemberService          = $chatMemberService;
		$this->createTaskService          = $createTaskService;
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
			foreach ($jsonData as $requestId => $requestPayload) {
				$request = $this->requestRepository->findOneOrThrow(TypeConverterHelper::toInt($requestId));

				$this->handleRequest($request, $requestPayload, $survey);
			}

			$tx->commit();
		} catch (Throwable $th) {
			$tx->rollback();
			throw $th;
		}
	}

	/**
	 * @throws ErrorException
	 * @throws SaveModelException
	 * @throws Throwable
	 * @throws ModelNotFoundException
	 */
	private function handleRequest(Request $request, array $payload, Survey $survey): void
	{
		$answer = ArrayHelper::getValue($payload, 'answer');

		if (is_null($answer)) {
			return;
		}

		$answer = TypeConverterHelper::toInt($answer);

		$tx = $this->transactionBeginner->begin();

		try {
			$this->markRequestAsCalled($request, $survey);

			if ($answer === self::ANSWER_REQUEST_MUST_BE_DISABLED && !$request->isPassive()) {
				$this->disableRequest($request, $payload, $survey);
			} else {
				if ($answer === self::ANSWER_REQUEST_MUST_BE_EDITED) {
					$this->createEditingTask($request, $payload, $survey);
				}
			}

			$tx->commit();
		} catch (Throwable $th) {
			$tx->rollback();
			throw $th;
		}
	}

	/**
	 * @throws ErrorException
	 * @throws SaveModelException
	 */
	private function markRequestAsCalled(Request $request, Survey $survey): void
	{
		$call = $survey->mainCall;

		if (!is_null($call)) {
			$this->chatMemberService->linkCall($request->chatMember, $call);
		}
	}

	/**
	 * @throws Throwable
	 * @throws SaveModelException
	 */
	private function disableRequest(Request $request, array $payload, Survey $survey): void
	{
		$tx = $this->transactionBeginner->begin();

		try {
			$this->requestService->markAsPassive($request, new PassiveRequestDto([
				'passive_why'         => Request::PASSIVE_WHY_SURVEY,
				'passive_why_comment' => ArrayHelper::getValue($payload, 'passive_why_comment')
			]));

			$this->effectSystemMessageService->createSystemMessage($request->chatMember, $survey, self::DISABLE_MESSAGE);

			$tx->commit();
		} catch (Throwable $th) {
			$tx->rollback();
			throw $th;
		}
	}

	private function createEditingTask(Request $request, array $payload, Survey $survey): void
	{
		$tx = $this->transactionBeginner->begin();

		try {
			$taskDto = $this->taskBuilderFactory->createEffectBuilder()
			                                    ->setType(Task::TYPE_REQUEST_HANDLING)
			                                    ->setCreatedBy($survey->user)
			                                    ->setTitle($this->getEditingTaskTitle($request))
			                                    ->setMessage(ArrayHelper::getValue($payload, 'description'))
			                                    ->build();

			$relationsDtos = [
				$this->makeTaskRelationEntityDto($request),
				$this->makeTaskRelationEntityDto($survey)
			];

			$this->createTaskService->create($taskDto, [], $relationsDtos);

			$tx->commit();
		} catch (Throwable $th) {
			$tx->rollback();
			throw $th;
		}
	}

	private function getEditingTaskTitle(Request $request): string
	{
		return sprintf(self::EDIT_TASK_MESSAGE, $request->id, $request->company->getShortName());
	}

	private function makeTaskRelationEntityDto($entity): LinkTaskRelationEntityDto
	{
		return new LinkTaskRelationEntityDto([
			'entityId'     => $entity->id,
			'entityType'   => $entity::getMorphClass(),
			'relationType' => TaskRelationEntity::RELATION_TYPE_SYSTEM
		]);
	}
}