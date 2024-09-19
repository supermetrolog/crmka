<?php

declare(strict_types=1);

namespace app\usecases\ChatMember;

use app\components\Notification\Factories\NotifierFactory;
use app\components\Notification\Notification;
use app\dto\Alert\CreateAlertDto;
use app\dto\ChatMember\CreateChatMemberLastEventDto;
use app\dto\ChatMember\CreateChatMemberMessageDto;
use app\dto\ChatMember\CreateChatMemberMessageViewDto;
use app\dto\ChatMember\UpdateChatMemberMessageDto;
use app\dto\Media\CreateMediaDto;
use app\dto\Notification\CreateNotificationDto;
use app\dto\Relation\CreateRelationDto;
use app\dto\Reminder\CreateReminderDto;
use app\dto\Task\CreateTaskDto;
use app\kernel\common\database\interfaces\transaction\TransactionBeginnerInterface;
use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\Alert;
use app\models\ChatMember;
use app\models\ChatMemberMessage;
use app\models\ChatMemberMessageTag;
use app\models\ChatMemberMessageView;
use app\models\Contact;
use app\models\Notification\UserNotification;
use app\models\Relation;
use app\models\Reminder;
use app\models\Task;
use app\models\User;
use app\repositories\ChatMemberMessageRepository;
use app\usecases\Alert\CreateAlertService;
use app\usecases\Media\CreateMediaService;
use app\usecases\Relation\RelationService;
use app\usecases\Reminder\CreateReminderService;
use app\usecases\Task\CreateTaskService;
use Throwable;
use yii\db\Exception;
use yii\db\Expression;

class ChatMemberMessageService
{
	private TransactionBeginnerInterface   $transactionBeginner;
	protected CreateTaskService            $createTaskService;
	protected CreateAlertService           $createAlertService;
	protected CreateReminderService        $createReminderService;
	protected CreateMediaService           $createMediaService;
	protected ChatMemberMessageViewService $chatMemberMessageViewService;
	protected RelationService              $relationService;
	protected NotifierFactory              $notifierFactory;
	protected ChatMemberMessageRepository  $chatMemberMessageRepository;
	protected ChatMemberLastEventService   $chatMemberLastEventService;

	public function __construct(
		TransactionBeginnerInterface $transactionBeginner,
		CreateTaskService $createTaskService,
		RelationService $relationService,
		CreateAlertService $createAlertService,
		CreateReminderService $createReminderService,
		CreateMediaService $createMediaService,
		ChatMemberMessageViewService $chatMemberMessageViewService,
		NotifierFactory $notifierFactory,
		ChatMemberMessageRepository $chatMemberMessageRepository,
		ChatMemberLastEventService $chatMemberLastEventService
	)
	{
		$this->transactionBeginner          = $transactionBeginner;
		$this->createTaskService            = $createTaskService;
		$this->relationService              = $relationService;
		$this->createAlertService           = $createAlertService;
		$this->createReminderService        = $createReminderService;
		$this->createMediaService           = $createMediaService;
		$this->chatMemberMessageViewService = $chatMemberMessageViewService;
		$this->notifierFactory              = $notifierFactory;
		$this->chatMemberMessageRepository  = $chatMemberMessageRepository;
		$this->chatMemberLastEventService   = $chatMemberLastEventService;
	}

	/**
	 * @param CreateMediaDto[] $mediaDtos
	 *
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	public function create(CreateChatMemberMessageDto $dto, array $mediaDtos = []): ChatMemberMessage
	{
		$tx = $this->transactionBeginner->begin();

		try {
			$message = new ChatMemberMessage();

			$message->from_chat_member_id = $dto->from->id;
			$message->to_chat_member_id   = $dto->to->id;

			$message->message = $dto->message;

			$message->saveOrThrow();

			foreach ($dto->contactIds as $contactId) {
				$this->relationService->create(new CreateRelationDto([
					'first_type'  => $message::getMorphClass(),
					'first_id'    => $message->id,
					'second_type' => Contact::getMorphClass(),
					'second_id'   => $contactId,
				]));
			}

			foreach ($dto->tagIds as $tagId) {
				$this->relationService->create(new CreateRelationDto([
					'first_type'  => $message::getMorphClass(),
					'first_id'    => $message->id,
					'second_type' => ChatMemberMessageTag::getMorphClass(),
					'second_id'   => $tagId,
				]));
			}

			$message->refresh();

			foreach ($mediaDtos as $mediaDto) {
				$media = $this->createMediaService->create($mediaDto);

				$this->relationService->create(new CreateRelationDto([
					'first_type'  => $message::getMorphClass(),
					'first_id'    => $message->id,
					'second_type' => $media::getMorphClass(),
					'second_id'   => $media->id,
				]));
			}

			$this->markMessageAsRead($message, $message->from_chat_member_id);

			$this->markChatAsLatestForMember($message->to_chat_member_id, $message->from_chat_member_id);

			$tx->commit();

			return $message;
		} catch (Throwable $th) {
			$tx->rollback();
			throw $th;
		}
	}


	/**
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	public function update(ChatMemberMessage $message, UpdateChatMemberMessageDto $dto): ChatMemberMessage
	{
		$tx = $this->transactionBeginner->begin();

		try {
			$message->message = $dto->message;

			$message->saveOrThrow();

			$query = Relation::find()
			                 ->byFirst($message->id, $message::getMorphClass())
			                 ->bySecondType(Contact::getMorphClass())
			                 ->notSecondIds($dto->contactIds);

			$this->relationService->deleteByQuery($query);

			$query = Relation::find()
			                 ->byFirst($message->id, $message::getMorphClass())
			                 ->bySecondType(ChatMemberMessageTag::getMorphClass())
			                 ->notSecondIds($dto->tagIds);

			$this->relationService->deleteByQuery($query);

			foreach ($dto->contactIds as $contactId) {
				$this->relationService->createIfNotExists(new CreateRelationDto([
					'first_type'  => $message::getMorphClass(),
					'first_id'    => $message->id,
					'second_type' => Contact::getMorphClass(),
					'second_id'   => $contactId,
				]));
			}

			foreach ($dto->tagIds as $tagId) {
				$this->relationService->createIfNotExists(new CreateRelationDto([
					'first_type'  => $message::getMorphClass(),
					'first_id'    => $message->id,
					'second_type' => ChatMemberMessageTag::getMorphClass(),
					'second_id'   => $tagId,
				]));
			}

			$tx->commit();

			return $message;
		} catch (Throwable $th) {
			$tx->rollback();
			throw $th;
		}
	}

	/**
	 * @throws SaveModelException
	 * @throws Exception
	 * @throws Throwable
	 */
	public function createWithTask(CreateChatMemberMessageDto $createChatMemberMessageDto, CreateTaskDto $createTaskDto): ChatMemberMessage
	{
		$tx = $this->transactionBeginner->begin();

		try {
			$message = $this->create($createChatMemberMessageDto);
			$this->createTask($message, $createTaskDto);

			$tx->commit();

			return $message;
		} catch (Throwable $th) {
			$tx->rollBack();
			throw $th;
		}
	}

	/**
	 * @throws SaveModelException
	 * @throws Exception
	 * @throws Throwable
	 */
	public function createTask(ChatMemberMessage $message, CreateTaskDto $createTaskDto): Task
	{
		$tx = $this->transactionBeginner->begin();

		try {
			$task = $this->createTaskService->create($createTaskDto);

			$this->relationService->create(new CreateRelationDto([
				'first_type'  => $message::getMorphClass(),
				'first_id'    => $message->id,
				'second_type' => $task::getMorphClass(),
				'second_id'   => $task->id,
			]));

			if ($task->user_id !== $task->created_by_id) {
				$this->markMessageAsUnreadForChatMember($message, User::getMorphClass(), $task->user_id);
			}

			$this->markChatAsLatestForModel($message->to_chat_member_id, User::getMorphClass(), $task->user_id);

			$tx->commit();

			return $task;
		} catch (Throwable $th) {
			$tx->rollBack();
			throw $th;
		}
	}

	/**
	 * @throws SaveModelException
	 * @throws Exception
	 * @throws Throwable
	 */
	public function createAlert(ChatMemberMessage $message, CreateAlertDto $createAlertDto): Alert
	{
		$tx = $this->transactionBeginner->begin();

		try {
			$alert = $this->createAlertService->create($createAlertDto);

			$this->relationService->create(new CreateRelationDto([
				'first_type'  => $message::getMorphClass(),
				'first_id'    => $message->id,
				'second_type' => $alert::getMorphClass(),
				'second_id'   => $alert->id,
			]));

			$tx->commit();

			return $alert;
		} catch (Throwable $th) {
			$tx->rollBack();
			throw $th;
		}
	}

	/**
	 * @throws SaveModelException
	 * @throws Exception
	 * @throws Throwable
	 */
	public function createReminder(ChatMemberMessage $message, CreateReminderDto $createReminderDto): Reminder
	{
		$tx = $this->transactionBeginner->begin();

		try {
			$reminder = $this->createReminderService->create($createReminderDto);

			$this->relationService->create(new CreateRelationDto([
				'first_type'  => $message::getMorphClass(),
				'first_id'    => $message->id,
				'second_type' => $reminder::getMorphClass(),
				'second_id'   => $reminder->id,
			]));

			$this->markMessageAsUnreadForChatMember($message, User::getMorphClass(), $reminder->user_id);

			$this->markChatAsLatestForModel($message->to_chat_member_id, User::getMorphClass(), $reminder->user_id);

			$tx->commit();

			return $reminder;
		} catch (Throwable $th) {
			$tx->rollBack();
			throw $th;
		}
	}

	/**
	 * @throws SaveModelException
	 * @throws Exception
	 * @throws Throwable
	 */
	public function createNotification(ChatMemberMessage $message, CreateNotificationDto $dto): UserNotification
	{
		$tx = $this->transactionBeginner->begin();

		try {
			$userNotification = $this->notifierFactory
				->create()
				->setChannel($dto->channel)
				->setNotification(new Notification($dto->subject, $dto->message))
				->setNotifiable($dto->notifiable)
				->setCreatedByType($dto->created_by_type)
				->setCreatedById($dto->created_by_id)
				->send();

			$this->relationService->create(new CreateRelationDto([
				'first_type'  => $message::getMorphClass(),
				'first_id'    => $message->id,
				'second_type' => $userNotification::getMorphClass(),
				'second_id'   => $userNotification->id,
			]));

			$this->markMessageAsUnreadForChatMember($message, User::getMorphClass(), $userNotification->user_id);

			$this->markChatAsLatestForModel($message->to_chat_member_id, User::getMorphClass(), $userNotification->user_id);

			$tx->commit();

			return $userNotification;
		} catch (Throwable $th) {
			$tx->rollBack();
			throw $th;
		}
	}

	public function viewMessages(ChatMemberMessage $message, int $from_chat_member_id): void
	{
		$tx = $this->transactionBeginner->begin();

		try {
			foreach ($this->chatMemberMessageRepository->findPreviousUnreadByMessage($message, $from_chat_member_id) as $unreadMessage) {
				$this->markMessageAsRead($unreadMessage, $from_chat_member_id);
			}

			$this->markChatAsLatestForMember($message->to_chat_member_id, $from_chat_member_id);

			$tx->commit();
		} catch (Throwable $th) {
			$tx->rollBack();
			throw $th;
		}
	}

	/**
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	private function markMessageAsRead(ChatMemberMessage $message, int $from_chat_member_id): void
	{
		$this->chatMemberMessageViewService->create(new CreateChatMemberMessageViewDto([
			'message'             => $message,
			'from_chat_member_id' => $from_chat_member_id,
		]));
	}

	/**
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	private function markMessageAsUnreadForChatMember(ChatMemberMessage $message, string $model_type, int $model_id): void
	{
		$view = ChatMemberMessageView::find()
		                             ->leftJoin(ChatMember::getTable(), [
			                             ChatMember::field('id')         => new Expression(ChatMemberMessageView::field('chat_member_id')),
			                             ChatMember::field('model_type') => $model_type,
			                             ChatMember::field('model_id')   => $model_id,
		                             ])
		                             ->andWhere([ChatMemberMessageView::field('chat_member_message_id') => $message->id])
		                             ->andWhereNotNull(ChatMember::field('id'))
		                             ->one();

		if ($view === null) {
			return;
		}

		$this->chatMemberMessageViewService->delete($view);
	}

	/**
	 * @throws SaveModelException
	 * @throws ModelNotFoundException
	 */
	private function markChatAsLatestForModel(int $chat_id, string $model_type, int $model_id): void
	{
		$chatMember = ChatMember::find()
		                        ->byModelType($model_type)
		                        ->byModelId($model_id)
		                        ->one();

		$this->markChatAsLatestForMember($chat_id, $chatMember->id);
	}

	/**
	 * @throws SaveModelException
	 * @throws ModelNotFoundException
	 */
	private function markChatAsLatestForMember(int $chat_id, int $member_id): void
	{
		$this->chatMemberLastEventService->updateOrCreate(new CreateChatMemberLastEventDto([
			'chat_member_id'       => $member_id,
			'event_chat_member_id' => $chat_id,
		]));
	}
}