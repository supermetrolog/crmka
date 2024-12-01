<?php

namespace app\components\EffectStrategy;

use app\dto\ChatMember\CreateChatMemberSystemMessageDto;
use app\dto\Task\CreateTaskDto;
use app\helpers\DateIntervalHelper;
use app\helpers\DateTimeHelper;
use app\kernel\common\database\interfaces\transaction\TransactionBeginnerInterface;
use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\ChatMember;
use app\models\ChatMemberMessage;
use app\models\ObjectChatMember;
use app\models\QuestionAnswer;
use app\models\Survey;
use app\models\SurveyQuestionAnswer;
use app\models\Task;
use app\models\TaskTag;
use app\models\User;
use app\services\ChatMemberSystemMessage\ObjectHasEquipmentForSaleSystemMessage;
use app\usecases\ChatMember\ChatMemberMessageService;
use Throwable;
use yii\base\Exception;

class ObjectHasEquipmentForSaleEffectStrategy extends AbstractEffectStrategy
{
	private const TASK_MESSAGE_TEXT       = 'Объект %s - есть оборудование под продажу, нужно обработать.';
	private const DAYS_FOR_TASK_EXECUTION = 7; // days

	private ChatMemberMessageService     $chatMemberMessageService;
	private TransactionBeginnerInterface $transactionBeginner;

	public function __construct(
		ChatMemberMessageService $chatMemberMessageService,
		TransactionBeginnerInterface $transactionBeginner
	)
	{
		$this->chatMemberMessageService = $chatMemberMessageService;
		$this->transactionBeginner      = $transactionBeginner;
	}

	public function shouldBeProcessed(Survey $survey, QuestionAnswer $answer): bool
	{
		return $answer->surveyQuestionAnswer->getMaybeBool();
	}

	/**
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	public function process(Survey $survey, SurveyQuestionAnswer $surveyQuestionAnswer): void
	{
		$chatMember      = $survey->chatMember;
		$chatMemberModel = $chatMember->model;

		$tx = $this->transactionBeginner->begin();

		try {
			$message = $this->sendSystemMessageIntoObject($chatMember, $survey);

			$this->createTaskForMessage($message, $survey->user, $chatMemberModel);

			$tx->commit();
		} catch (Throwable $th) {
			$tx->rollback();
			throw $th;
		}
	}

	/**
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	private function sendSystemMessageIntoObject(ChatMember $chatMember, Survey $survey): ChatMemberMessage
	{
		$message = ObjectHasEquipmentForSaleSystemMessage::create()
		                                                 ->setSurveyId($survey->id)
		                                                 ->toMessage();

		$dto = new CreateChatMemberSystemMessageDto([
			'message'    => $message,
			'to'         => $chatMember,
			'surveyIds'  => [$survey->id],
			'contactIds' => [$survey->contact_id],
		]);

		return $this->chatMemberMessageService->createSystemMessage($dto);
	}

	public function getTaskMessageText(ObjectChatMember $objectChatMember): string
	{
		return sprintf(self::TASK_MESSAGE_TEXT, $objectChatMember->object_id);
	}

	/**
	 * @throws ModelNotFoundException
	 * @throws SaveModelException
	 * @throws Throwable
	 * @throws Exception
	 */
	protected function createTaskForMessage(ChatMemberMessage $message, User $user, ObjectChatMember $objectChatMember): void
	{
		$moderator = $this->userRepository->getModerator();

		if (!$moderator) {
			throw new ModelNotFoundException('Moderator not found');
		}

		$dto = new CreateTaskDto([
			'user'            => $moderator,
			'message'         => $this->getTaskMessageText($objectChatMember),
			'status'          => Task::STATUS_CREATED,
			'start'           => DateTimeHelper::now(),
			'end'             => DateTimeHelper::now()
			                                   ->add(DateIntervalHelper::days(self::DAYS_FOR_TASK_EXECUTION)),
			'created_by_type' => $user::getMorphClass(),
			'created_by_id'   => $user->id,
			'tagIds'          => [TaskTag::SURVEY_TASK_TAG_ID],
			'observerIds'     => []
		]);

		$this->chatMemberMessageService->createTask($message, $dto);
	}
}