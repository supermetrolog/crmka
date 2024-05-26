<?php

declare(strict_types=1);

namespace app\components\Notification\Interfaces;

interface StoredNotificationInterface extends NotificationInterface
{
	public function getId(): int;

	public function getSubject(): string;

	public function getMessage(): string;
}