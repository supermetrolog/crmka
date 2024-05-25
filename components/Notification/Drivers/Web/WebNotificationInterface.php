<?php

declare(strict_types=1);

namespace app\components\Notification\Drivers\Web;

interface WebNotificationInterface
{
	public function getSubject(): string;

	public function getContent(): string;
}