<?php

declare(strict_types=1);

namespace app\components\Notification\Interfaces;

interface NotificationInterface
{
	public function getSubject(): string;

	public function getMessage(): string;
}