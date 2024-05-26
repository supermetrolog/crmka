<?php

declare(strict_types=1);

namespace app\components\Notification;

use app\components\Notification\Factories\NotificationDriverFactory;
use app\components\Notification\Interfaces\NotifiableInterface;
use app\components\Notification\Interfaces\NotificationInterface;
use app\dto\Mailing\CreateMailingDto;
use app\dto\UserNotification\CreateUserNotificationDto;
use app\kernel\common\database\interfaces\transaction\TransactionBeginnerInterface;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\ActiveQuery\NotificationChannelQuery;
use app\models\Notification\UserNotification;
use app\usecases\Mailing\MailingService;
use app\usecases\Notification\UserNotificationService;
use DateTime;
use Throwable;
use yii\base\ErrorException;

class Notifier
{
	private string                $channel;
	private bool                  $sendNow = true;
	private NotificationInterface $notification;
	private NotifiableInterface   $notifiable;

	private string $createdByType;
	private int    $createdById;

	private NotificationDriverFactory    $notificationDriverFactory;
	private NotificationChannelQuery     $notificationChannelQuery;
	private TransactionBeginnerInterface $transactionBeginner;
	private MailingService               $mailingService;
	private UserNotificationService      $userNotificationService;

	public function __construct(
		NotificationDriverFactory $notificationDriverFactory,
		NotificationChannelQuery $notificationChannelQuery,
		TransactionBeginnerInterface $transactionBeginner,
		MailingService $mailingService,
		UserNotificationService $userNotificationService
	)
	{
		$this->notificationDriverFactory = $notificationDriverFactory;
		$this->notificationChannelQuery  = $notificationChannelQuery;
		$this->transactionBeginner       = $transactionBeginner;
		$this->mailingService            = $mailingService;
		$this->userNotificationService   = $userNotificationService;
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
			$channel = $this->notificationChannelQuery->bySlug($this->channel)->one();

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
				'notified_at' => $this->sendNow ? new DateTime() : null,
			]));


			if ($this->sendNow) {
				$this->notificationDriverFactory
					->fromChannel($channel)
					->send($this->notifiable, $this->notification);

				$tx->commit();
				return $userNotification;
			} else {
				// TODO: push job
				$tx->commit();
				return $userNotification;
			}
		} catch (Throwable $th) {
			$tx->rollback();
			throw $th;
		}
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