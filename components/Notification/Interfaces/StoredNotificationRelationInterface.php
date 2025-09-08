<?php

declare(strict_types=1);

namespace app\components\Notification\Interfaces;

interface StoredNotificationRelationInterface extends NotificationRelationInterface
{
	public function getId(): int;
}