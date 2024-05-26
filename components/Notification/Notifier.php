<?php

declare(strict_types=1);

namespace app\components\Notification;

use app\components\Notification\Factories\NotificationDriverFactory;
use app\models\ActiveQuery\NotificationChannelQuery;
use yii\base\ErrorException;

class Notifier
{
	private array                $channels = [];
	private bool                 $sendNow  = false;
	private AbstractNotification $notification;
	private AbstractNotifiable   $notifiable;

	private NotificationDriverFactory $notificationDriverFactory;
	private NotificationChannelQuery  $notificationChannelQuery;

	public function __construct(
		NotificationDriverFactory $notificationDriverFactory,
		NotificationChannelQuery $notificationChannelQuery
	)
	{
		$this->notificationDriverFactory = $notificationDriverFactory;
		$this->notificationChannelQuery  = $notificationChannelQuery;
	}

	/**
	 * @return array
	 * @throws ErrorException
	 */
	public function send(): array
	{
		$channels = $this->notificationChannelQuery->bySlugs($this->channels)->all();

		if (!$this->channels) {
			throw new ErrorException('Channels not found');
		}

		foreach ($channels as $channel) {
			// TODO: create model

			if ($this->sendNow) {
				$driver = $this->notificationDriverFactory->fromChannel($channel);
				$driver->send($this->notifiable, $this->notification);
			} else {
				// TODO: push job
			}
		}

	}

	public function addChannel(string $channel): self
	{
		$this->channels[] = $channel;

		return $this;
	}

	public function setChannels(array $channels): self
	{
		$this->channels = $channels;

		return $this;
	}

	public function setNotification(AbstractNotification $notification): self
	{
		$this->notification = $notification;

		return $this;
	}

	public function setNotifiable(AbstractNotifiable $notifiable): self
	{
		$this->notifiable = $notifiable;

		return $this;
	}

	public function setSendNow(bool $sendNow): self
	{
		$this->sendNow = $sendNow;

		return $this;
	}
}