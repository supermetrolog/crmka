<?php

declare(strict_types=1);

namespace app\usecases\ChatMember;

use app\components\Notification\Factories\NotifierFactory;
use app\components\Notification\Notification;
use app\dto\Alert\CreateAlertDto;
use app\dto\ChatMember\CreateChatMemberLastEventDto;
use app\dto\ChatMember\CreateChatMemberMessageDto;
use app\dto\ChatMember\CreateChatMemberMessageViewDto;
use app\dto\ChatMember\CreateChatMemberSystemMessageDto;
use app\dto\ChatMember\UpdateChatMemberMessageDto;
use app\dto\Media\CreateMediaDto;
use app\dto\Notification\CreateNotificationDto;
use app\dto\Relation\CreateRelationDto;
use app\dto\Reminder\CreateReminderDto;
use app\dto\Task\CreateTaskDto;
use app\helpers\ArrayHelper;
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
use app\models\Survey;
use app\models\Task;
use app\models\User;
use app\repositories\ChatMemberMessageRepository;
use app\repositories\ChatMemberRepository;
use app\usecases\Alert\CreateAlertService;
use app\usecases\Media\CreateMediaService;
use app\usecases\Media\MediaService;
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
	protected MediaService                 $mediaService;
	protected ChatMemberRepository         $chatMemberRepository;

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
		ChatMemberLastEventService $chatMemberLastEventService,
		MediaService $mediaService,
		ChatMemberRepository $chatMemberRepository
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
		$this->mediaService                 = $mediaService;
		$this->chatMemberRepository         = $chatMemberRepository;
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
			$message->message             = $dto->message;
			$message->template            = $dto->template;

			if ($dto->replyTo !== null) {
				$message->reply_to_id = $dto->replyTo->id;
				$this->markChatAsLatestForMember($message->to_chat_member_id, $dto->replyTo->from_chat_member_id);
			}

			$message->saveOrThrow();

			foreach ($dto->contactIds as $contactId) {
				$this->linkRelation($message, Contact::getMorphClass(), $contactId);
			}

			foreach ($dto->tagIds as $tagId) {
				$this->linkRelation($message, ChatMemberMessageTag::getMorphClass(), $tagId);
			}

			foreach ($dto->surveyIds as $surveyId) {
				$this->linkRelation($message, Survey::getMorphClass(), $surveyId);
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

			$this->markMessageAsRead($message, $message->fromChatMember);

			$this->markChatAsLatestForMember($message->to_chat_member_id, $message->from_chat_member_id);

			$tx->commit();

			return $message;
		} catch (Throwable $th) {
			$tx->rollback();
			throw $th;
		}
	}

	/**
	 * @param string|int $relationId
	 *
	 * @throws SaveModelException
	 */
	private function linkRelation(ChatMemberMessage $message, string $relationType, $relationId): void
	{
		$this->relationService->create(new CreateRelationDto([
			'first_type'  => $message::getMorphClass(),
			'first_id'    => $message->id,
			'second_type' => $relationType,
			'second_id'   => $relationId,
		]));
	}

	/**
	 * @param string|int $relationId
	 *
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	private function linkIfNotExistsRelation(ChatMemberMessage $message, string $relationType, $relationId): void
	{
		$this->relationService->createIfNotExists(new CreateRelationDto([
			'first_type'  => $message::getMorphClass(),
			'first_id'    => $message->id,
			'second_type' => $relationType,
			'second_id'   => $relationId
		]));
	}

	/**
	 * @param array<int|string> $relationIds
	 *
	 * @throws Throwable
	 */
	private function unlinkDeletedRelations(ChatMemberMessage $message, string $relationType, array $relationIds): void
	{
		$query = Relation::find()
		                 ->byFirst($message->id, $message::getMorphClass())
		                 ->bySecondType($relationType)
		                 ->notSecondIds($relationIds);

		$this->relationService->deleteByQuery($query);
	}

	/**
	 * @param array<string|int> $relationIds
	 *
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	private function updateRelations(ChatMemberMessage $message, string $relationType, array $relationIds): void
	{
		$this->unlinkDeletedRelations($message, $relationType, $relationIds);

		foreach ($relationIds as $relationId) {
			$this->linkIfNotExistsRelation($message, $relationType, $relationId);
		}
	}

	/**
	 * @param ChatMemberMessage          $message
	 * @param UpdateChatMemberMessageDto $dto
	 * @param CreateMediaDto[]           $mediaDtos
	 *
	 * @return ChatMemberMessage
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	public function update(ChatMemberMessage $message, UpdateChatMemberMessageDto $dto, array $mediaDtos = []): ChatMemberMessage
	{
		$tx = $this->transactionBeginner->begin();

		try {
			$message->message = $dto->message;

			$message->saveOrThrow();

			$this->updateRelations($message, Contact::getMorphClass(), $dto->contactIds);
			$this->updateRelations($message, ChatMemberMessageTag::getMorphClass(), $dto->tagIds);
			$this->updateRelations($message, Survey::getMorphClass(), $dto->surveyIds);

			$deletedMedias = $message->getFiles()->andWhere(['not in', 'id', $dto->currentFiles])->all();

			foreach ($deletedMedias as $media) {
				$this->mediaService->delete($media);
			}

			foreach ($mediaDtos as $mediaDto) {
				$media = $this->createMediaService->create($mediaDto);

				$this->relationService->create(new CreateRelationDto([
					'first_type'  => $message::getMorphClass(),
					'first_id'    => $message->id,
					'second_type' => $media::getMorphClass(),
					'second_id'   => $media->id,
				]));
			}

			$tx->commit();

			$message->refresh();

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
	 * @param CreateTaskDto[] $createTaskDtos
	 *
	 * @throws SaveModelException
	 * @throws Exception
	 * @throws Throwable
	 */
	public function createWithTasks(CreateChatMemberMessageDto $createChatMemberMessageDto, array $createTaskDtos = []): ChatMemberMessage
	{
		$tx = $this->transactionBeginner->begin();

		try {
			$message = $this->create($createChatMemberMessageDto);

			foreach ($createTaskDtos as $createTaskDto) {
				$this->createTask($message, $createTaskDto);
			}

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

			$this->linkRelation($message, Task::getMorphClass(), $task->id);

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
	 * @param CreateTaskDto[] $createTaskDtos
	 *
	 * @return Task[]
	 * @throws SaveModelException
	 * @throws Exception
	 * @throws Throwable
	 */
	public function createTasks(ChatMemberMessage $message, array $createTaskDtos): array
	{
		$tx = $this->transactionBeginner->begin();

		try {
			$tasks = [];

			foreach ($createTaskDtos as $dto) {
				$task = $this->createTaskService->create($dto);

				$this->linkRelation($message, Task::getMorphClass(), $task->id);

				if ($task->user_id !== $task->created_by_id) {
					$this->markMessageAsUnreadForChatMember($message, User::getMorphClass(), $task->user_id);
				}

				$tasks[] = $task;
			}

			$uniqueUserIds = ArrayHelper::uniqueByKey($tasks, 'user_id');

			foreach ($uniqueUserIds as $userId) {
				$this->markChatAsLatestForModel($message->to_chat_member_id, User::getMorphClass(), $userId);
			}

			$tx->commit();

			return $tasks;
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

			$this->linkRelation($message, Reminder::getMorphClass(), $reminder->id);

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

			$this->linkRelation($message, UserNotification::getMorphClass(), $userNotification->id);

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
			/** @var ChatMember $fromChatMember */
			$fromChatMember = ChatMember::find()->byId($from_chat_member_id)->one();

			foreach ($this->chatMemberMessageRepository->findPreviousUnreadByMessage($message, $from_chat_member_id) as $unreadMessage) {
				$this->markMessageAsRead($unreadMessage, $fromChatMember);
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
	private function markMessageAsRead(ChatMemberMessage $message, ChatMember $fromChatMember): void
	{
		$this->chatMemberMessageViewService->create(new CreateChatMemberMessageViewDto([
			'message'        => $message,
			'fromChatMember' => $fromChatMember
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

	/**
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	public function createSystemMessage(CreateChatMemberSystemMessageDto $dto): ChatMemberMessage
	{
		$chatMemberMessageDto = new CreateChatMemberMessageDto([
			'from'       => $this->chatMemberRepository->getSystemChatMember(),
			'to'         => $dto->to,
			'replyTo'    => $dto->replyTo,
			'message'    => $dto->message,
			'contactIds' => $dto->contactIds,
			'tagIds'     => $dto->tagIds,
			'surveyIds'  => $dto->surveyIds,
			'template'   => $dto->template
		]);

		return $this->create($chatMemberMessageDto);
	}
}