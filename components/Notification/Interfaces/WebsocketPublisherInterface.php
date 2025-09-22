<?php

declare(strict_types=1);

namespace app\components\Notification\Interfaces;

interface WebsocketPublisherInterface
{
	public function publishToUser(int $userId, array $payload, string $action): void;
}