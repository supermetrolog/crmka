<?php

declare(strict_types=1);

namespace app\components\Notification\Drivers\Web;

use app\components\Notification\Interfaces\NotifiableInterface;
use app\components\Notification\Interfaces\NotificationChannelDriverInterface;
use app\components\Notification\Interfaces\StoredNotificationInterface;
use app\dto\WebNotification\CreateWebNotificationDto;
use app\kernel\common\models\exceptions\SaveModelException;
use app\usecases\WebNotification\WebNotificationService;

class WebNotificationChannelDriver implements NotificationChannelDriverInterface
{
	private WebNotificationService $service;


	public function __construct(WebNotificationService $service)
	{
		$this->service = $service;
	}

	/**
	 * @throws SaveModelException
	 */
	public function send(NotifiableInterface $notifiable, StoredNotificationInterface $notification): void
	{
		$this->service->create(new CreateWebNotificationDto([
			'user_id'              => $notifiable->getUserId(),
			'user_notification_id' => $notification->getId(),
			'subject'              => $notification->getSubject(),
			'message'              => $notification->getMessage(),
		]));
	}
}