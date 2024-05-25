<?php

declare(strict_types=1);

namespace app\components\Notification\Interfaces;

use app\components\Notification\AbstractNotification;

interface NotificationChannelDriverInterface
{
	public function send($notifiable, AbstractNotification $notification): void;
}