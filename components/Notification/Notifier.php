<?php

declare(strict_types=1);

namespace app\components\Notification;

use app\components\Notification\Factories\NotificationDriverFactory;
use app\components\Notification\Interfaces\NotifiableInterface;
use app\components\Notification\Interfaces\NotificationActionInterface;
use app\components\Notification\Interfaces\NotificationInterface;
use app\components\Notification\Interfaces\NotificationRelationInterface;
use app\components\Notification\Interfaces\StoredNotificationInterface;
use app\dto\Mailing\CreateMailingDto;
use app\dto\UserNotification\CreateUserNotificationDto;
use app\dto\UserNotification\UserNotificationActionDto;
use app\dto\UserNotification\UserNotificationRelationDto;
use app\helpers\DateTimeHelper;
use app\kernel\common\database\interfaces\transaction\TransactionBeginnerInterface;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\ActiveQuery\NotificationChannelQuery;
use app\models\Notification\UserNotification;
use app\usecases\Mailing\MailingService;
use app\usecases\Notification\UserNotificationActionService;
use app\usecases\Notification\UserNotificationRelationService;
use app\usecases\Notification\UserNotificationService;
use Throwable;
use yii\base\ErrorException;

class Notifier
{
	private string                $channel;
	private bool                  $sendNow = true;
	private NotificationInterface $notification;
	private NotifiableInterface   $notifiable;

	private ?string $createdByType = null;
	private ?int    $createdById   = null;

	private NotificationDriverFactory       $notificationDriverFactory;
	private NotificationChannelQuery        $notificationChannelQuery;
	private TransactionBeginnerInterface    $transactionBeginner;
	private MailingService                  $mailingService;
	private UserNotificationService         $userNotificationService;
	private UserNotificationActionService   $userNotificationActionService;
	private UserNotificationRelationService $userNotificationRelationService;

	public function __construct(
		NotificationDriverFactory $notificationDriverFactory,
		NotificationChannelQuery $notificationChannelQuery,
		TransactionBeginnerInterface $transactionBeginner,
		MailingService $mailingService,
		UserNotificationService $userNotificationService,
		UserNotificationActionService $userNotificationActionService,
		UserNotificationRelationService $userNotificationRelationService
	)
	{
		$this->notificationDriverFactory       = $notificationDriverFactory;
		$this->notificationChannelQuery        = $notificationChannelQuery;
		$this->transactionBeginner             = $transactionBeginner;
		$this->mailingService                  = $mailingService;
		$this->userNotificationService         = $userNotificationService;
		$this->userNotificationActionService   = $userNotificationActionService;
		$this->userNotificationRelationService = $userNotificationRelationService;
	}

	/**
	 * @return UserNotification
	 * @throws ErrorException
	 * @throws Throwable
	 * @throws SaveModelException
	 */
	public function send(): UserNotification
	{
		$tx = $this->transactionBeginner->begin();

		try {
			$channel = $this->notificationChannelQuery->bySlug($this->channel)->oneOrThrow();

			$mailing = $this->mailingService->create(new CreateMailingDto([
				'channel_id'      => $channel->id,
				'subject'         => $this->notification->getSubject(),
				'message'         => $this->notification->getMessage(),
				'created_by_type' => $this->createdByType,
				'created_by_id'   => $this->createdById,
			]));

			$userNotification = $this->userNotificationService->create(new CreateUserNotificationDto([
				'mailing_id'  => $mailing->id,
				'user_id'     => $this->notifiable->getUserId(),
				'notified_at' => $this->sendNow ? DateTimeHelper::now() : null,
				'template'    => $this->notification->getTemplate()
			]));

			$userNotification->populateRelation('mailing', $mailing); // Чтобы не грузить из базы

			foreach ($this->notification->getActions() as $action) {
				$this->createAction($userNotification, $action);
			}

			foreach ($this->notification->getRelations() as $relation) {
				$this->createRelation($userNotification, $relation);
			}

			if ($this->sendNow) {
				$this->notificationDriverFactory
					->fromChannel($channel)
					->send($this->notifiable, $userNotification);

				$tx->commit();

				return $userNotification;
			} else {
				// TODO: push job
				throw new ErrorException('Not supported delayed send');
			}
		} catch (Throwable $th) {
			$tx->rollback();
			throw $th;
		}
	}

	/**
	 * @throws SaveModelException
	 */
	protected function createAction(StoredNotificationInterface $notification, NotificationActionInterface $action): void
	{
		$this->userNotificationActionService->create(new UserNotificationActionDto([
			'notificationId' => $notification->id,
			'type'           => $action->getType(),
			'code'           => $action->getCode(),
			'label'          => $action->getLabel(),
			'icon'           => $action->getIcon(),
			'style'          => $action->getStyle(),
			'confirmation'   => $action->needConfirmation(),
			'order'          => $action->getOrder(),
			'expiresAt'      => $action->getExpiresAt(),
			'payload'        => $action->getPayloadArray(),
		]));
	}

	/**
	 * @throws SaveModelException
	 */
	protected function createRelation(StoredNotificationInterface $notification, NotificationRelationInterface $relation): void
	{
		$this->userNotificationRelationService->create(new UserNotificationRelationDto([
			'notificationId' => $notification->id,
			'entityType'     => $relation->getEntityType(),
			'entityId'       => $relation->getEntityId(),
		]));
	}

	public function setChannel(string $channel): self
	{
		$this->channel = $channel;

		return $this;
	}

	public function setNotification(NotificationInterface $notification): self
	{
		$this->notification = $notification;

		return $this;
	}

	public function setNotifiable(NotifiableInterface $notifiable): self
	{
		$this->notifiable = $notifiable;

		return $this;
	}

	public function setSendNow(bool $sendNow): self
	{
		$this->sendNow = $sendNow;

		return $this;
	}

	public function setCreatedByType(string $createdByType): self
	{
		$this->createdByType = $createdByType;

		return $this;
	}

	public function setCreatedById(int $createdById): self
	{
		$this->createdById = $createdById;

		return $this;
	}
}