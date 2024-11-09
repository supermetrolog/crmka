<?php

namespace app\listeners\Survey;

use app\dto\ChatMember\CreateChatMemberSystemMessageDto;
use app\dto\Task\CreateTaskDto;
use app\events\Survey\SurveyRequestsNoLongerRelevantEvent;
use app\helpers\DateIntervalHelper;
use app\helpers\DateTimeHelper;
use app\kernel\common\database\interfaces\transaction\TransactionBeginnerInterface;
use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\kernel\common\models\exceptions\SaveModelException;
use app\listeners\EventListenerInterface;
use app\models\ChatMember;
use app\models\ChatMemberMessage;
use app\models\Survey;
use app\models\Task;
use app\models\TaskTag;
use app\models\User;
use app\repositories\UserRepository;
use app\services\ChatMemberSystemMessage\RequestsNoLongerRelevantChatMemberSystemMessage;
use app\usecases\ChatMember\ChatMemberMessageService;
use Exception;
use Throwable;
use yii\base\Event;


class SurveyRequestsNoLongerRelevantListener implements EventListenerInterface
{
	private const DAYS_FOR_TASK_EXECUTION = 7; // days
	private const TASK_MESSAGE_TEXT       = 'Запросы компании устарели, необходимо отправить их в пассив.';

	private ChatMemberMessageService     $chatMemberMessageService;
	private UserRepository               $userRepository;
	private TransactionBeginnerInterface $transactionBeginner;

	public function __construct(
		ChatMemberMessageService $chatMemberMessageService,
		UserRepository $userRepository,
		TransactionBeginnerInterface $transactionBeginner
	)
	{
		$this->chatMemberMessageService = $chatMemberMessageService;
		$this->userRepository           = $userRepository;
		$this->transactionBeginner      = $transactionBeginner;
	}

	/**
	 * @param SurveyRequestsNoLongerRelevantEvent $event
	 *
	 * @throws Throwable
	 * @throws SaveModelException
	 */
	public function handle(Event $event): void
	{
		$surveyId = $event->getSurveyId();

		$survey = Survey::find()->byId($surveyId)->oneOrThrow();

		$tx = $this->transactionBeginner->begin();

		try {
			$message = $this->sendSystemMessage($survey->chatMember, $survey);
			$this->createTask($message, $survey->user);

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
	private function sendSystemMessage(ChatMember $chatMember, Survey $survey): ChatMemberMessage
	{
		$message = RequestsNoLongerRelevantChatMemberSystemMessage::create()->toMessage();

		$dto = new CreateChatMemberSystemMessageDto([
			'message'    => $message,
			'to'         => $chatMember,
			'surveyIds'  => [$survey->id],
			'contactIds' => [$survey->contact_id],
		]);

		return $this->chatMemberMessageService->createSystemMessage($dto);
	}

	/**
	 * @throws SaveModelException
	 * @throws Throwable
	 * @throws Exception
	 */
	private function createTask(ChatMemberMessage $message, User $user): void
	{
		$moderator = $this->userRepository->getModerator();

		if (!$moderator) {
			throw new ModelNotFoundException('Moderator not found');
		}

		$dto = new CreateTaskDto([
			'user'            => $moderator,
			'message'         => self::TASK_MESSAGE_TEXT,
			'status'          => Task::STATUS_CREATED,
			'start'           => DateTimeHelper::nowf(),
			'end'             => DateTimeHelper::now()
			                                   ->add(DateIntervalHelper::days(self::DAYS_FOR_TASK_EXECUTION)),
			'created_by_type' => $user::getMorphClass(),
			'created_by_id'   => $user->id,
			'tagIds'          => [TaskTag::SURVEY_TASk_TAG_ID],
			'observerIds'     => []
		]);

		$this->chatMemberMessageService->createTask($message, $dto);
	}
}