<?php

declare(strict_types=1);

namespace app\components\Notification\Interfaces;

interface StoredNotificationActionInterface extends NotificationActionInterface
{
	public function getId(): int;
}