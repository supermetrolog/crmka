<?php

declare(strict_types=1);

namespace app\components\Notification;

use app\components\Notification\Factories\NotificationDriverFactory;
use app\models\ActiveQuery\NotificationChannelQuery;
use yii\base\Component;

class Notifier extends Component
{
	private array                $channels = [];
	private bool                 $sendNow  = false;
	private AbstractNotification $notification;
	private                      $notifiable; // TODO: type

	private NotificationDriverFactory $notificationDriverFactory;
	private NotificationChannelQuery  $notificationChannelQuery;

	public function send()
	{
		$channels = $this->notificationChannelQuery->bySlugs($this->channels)->all();

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
}