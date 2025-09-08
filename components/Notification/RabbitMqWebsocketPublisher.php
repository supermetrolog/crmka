<?php

declare(strict_types=1);

namespace app\components\Notification;

use app\components\Notification\Interfaces\WebsocketPublisherInterface;
use app\components\NotificationsQueueService;
use app\daemons\Message;

final class RabbitMqWebsocketPublisher implements WebsocketPublisherInterface
{
	protected NotificationsQueueService $queue;

	public function __construct(NotificationsQueueService $queue)
	{
		$this->queue = $queue;
	}

	public function publishToUser(int $userId, array $payload): void
	{
		$this->queue->publish([
			'consultant_id' => $userId,
			'payload'       => $payload,
			'ts'            => time(),
			'action'        => Message::ACTION_NEW_USER_NOTIFICATION,
		]);
	}
}