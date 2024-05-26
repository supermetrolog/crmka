<?php

declare(strict_types=1);

namespace app\components\Notification\Interfaces;

interface NotificationChannelDriverInterface
{
	public function send(NotifiableInterface $notifiable, StoredNotificationInterface $notification): void;
}