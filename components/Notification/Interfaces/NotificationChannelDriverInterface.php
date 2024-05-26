<?php

declare(strict_types=1);

namespace app\components\Notification\Interfaces;

use app\components\Notification\AbstractNotifiable;
use app\components\Notification\AbstractNotification;

interface NotificationChannelDriverInterface
{
	public function send(AbstractNotifiable $notifiable, AbstractNotification $notification): void;
}