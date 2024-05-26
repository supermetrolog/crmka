<?php

declare(strict_types=1);

namespace app\components\Notification\Factories;

use app\components\Notification\Drivers\Web\WebNotificationChannelDriver;
use app\components\Notification\Interfaces\NotificationChannelDriverInterface;
use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\models\ActiveQuery\NotificationChannelQuery;
use app\models\Notification\NotificationChannel;
use UnexpectedValueException;
use Yii;
use yii\base\ErrorException;
use yii\base\InvalidConfigException;
use yii\di\NotInstantiableException;

class NotificationDriverFactory
{
	private NotificationChannelQuery $notificationChannelQuery;

	public function __construct(NotificationChannelQuery $notificationChannelQuery)
	{
		$this->notificationChannelQuery = $notificationChannelQuery;
	}

	/**
	 * @param NotificationChannel $channel
	 *
	 * @return NotificationChannelDriverInterface
	 * @throws ErrorException
	 * @throws InvalidConfigException
	 * @throws NotInstantiableException
	 */
	public function fromChannel(NotificationChannel $channel): NotificationChannelDriverInterface
	{
		if (!$channel->is_enabled) {
			throw new ErrorException(sprintf('Notification channel [%s] disabled', $channel->slug));
		}

		switch ($channel->slug) {
			case NotificationChannel::WEB:
				return Yii::$container->get(WebNotificationChannelDriver::class);
			default:
				throw new UnexpectedValueException(sprintf('Driver for channel [%s] not found', $channel->slug));
		}
	}

	/**
	 *
	 * @param string $slug
	 *
	 * @return NotificationChannelDriverInterface
	 * @throws ErrorException
	 * @throws InvalidConfigException
	 * @throws ModelNotFoundException
	 * @throws NotInstantiableException
	 */
	public function fromChannelSlug(string $slug): NotificationChannelDriverInterface
	{
		$channel = $this->notificationChannelQuery->bySlug($slug)->oneOrThrow();

		return $this->fromChannel($channel);
	}
}