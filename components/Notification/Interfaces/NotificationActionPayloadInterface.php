<?php

declare(strict_types=1);

namespace app\components\Notification\Interfaces;

interface NotificationActionPayloadInterface
{
	public function toArray(): array;

	public static function fromArray(array $data);
}