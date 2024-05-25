<?php

declare(strict_types=1);

namespace app\components\Notification\Builders;

use app\components\Notification\AbstractNotification;
use app\components\Notification\Notifier;

class NotificationBuilder
{
	/** @var string[] */
	private array $channels = [];
	private bool                 $sendNow = false;
	private AbstractNotification $notification;
	private                      $notifiable; // TODO: type


	public function addChannel(string $channel): self
	{
		$this->channels[] = $channel;

		return $this;
	}

	public function sendNow(): self
	{
		$this->sendNow = true;

		return $this;
	}

	public function withNotifiable($notifiable): self
	{
		$this->notifiable = $notifiable;

		return $this;
	}

	public function withNotification(AbstractNotification $notification): self
	{
		$this->notification = $notification;

		return $this;
	}

	public function build(): Notifier
	{
		return new Notifier([
			'notifiable'   => $this->notifiable,
			'notification' => $this->notification,
			'sendNow'      => $this->sendNow,
			'channels'     => $this->channels
		]);
	}

}