<?php

declare(strict_types=1);

namespace app\components\Notification\Interfaces;

interface StoredNotificationTemplateInterface extends NotificationTemplateInterface
{
	public function getId(): int;
}