<?php

declare(strict_types=1);

namespace app\components\Notification\Factories;

use app\components\Notification\Drivers\Web\WebNotificationChannelDriver;
use app\components\Notification\Interfaces\NotificationChannelDriverInterface;
use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\models\ActiveQuery\NotificationChannelQuery;
use app\models\Notification\NotificationChannel;
use UnexpectedValueException;
use yii\base\ErrorException;

class NotificationDriverFactory
{
	private NotificationChannelQuery $notificationChannelQuery;

	public function __construct(NotificationChannelQuery $notificationChannelQuery)
	{
		$this->notificationChannelQuery = $notificationChannelQuery;
	}

	/**
	 * @throws ErrorException
	 */
	public function fromChannel(NotificationChannel $channel): NotificationChannelDriverInterface
	{
		if (!$channel->is_enabled) {
			throw new ErrorException(sprintf('Notification channel [%s] disabled', $channel->slug));
		}

		switch ($channel->slug) {
			case NotificationChannel::WEB:
				return new WebNotificationChannelDriver();
			default:
				throw new UnexpectedValueException(sprintf('Driver for channel [%s] not found', $channel->slug));
		}
	}

	/**90
	 *
	 * @throws ModelNotFoundException
	 * @throws ErrorException
	 */
	public function fromChannelSlug(string $slug): NotificationChannelDriverInterface
	{
		$channel = $this->notificationChannelQuery->bySlug($slug)->oneOrThrow();

		return $this->fromChannel($channel);
	}
}