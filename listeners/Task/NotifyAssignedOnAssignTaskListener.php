<?php

namespace app\listeners\Task;

use app\components\Notification\Factories\NotifierFactory;
use app\components\Notification\Interfaces\NotifiableInterface;
use app\components\Notification\Interfaces\NotificationInterface;
use app\enum\Notification\NotificationChannelSlugEnum;
use app\events\Task\AssignTaskEvent;
use app\factories\Notification\TaskNotificationFactory;
use app\kernel\common\models\exceptions\SaveModelException;
use app\listeners\EventListenerInterface;
use ErrorException;
use Throwable;
use yii\base\Event;
use yii\base\InvalidConfigException;
use yii\di\NotInstantiableException;


class NotifyAssignedOnAssignTaskListener implements EventListenerInterface
{
	private NotifierFactory         $notifierFactory;
	private TaskNotificationFactory $taskNotificationFactory;

	public function __construct(NotifierFactory $notifierFactory, TaskNotificationFactory $taskNotificationFactory)
	{
		$this->notifierFactory         = $notifierFactory;
		$this->taskNotificationFactory = $taskNotificationFactory;
	}

	/**
	 * @param AssignTaskEvent $event
	 *
	 * @throws Throwable
	 */
	public function handle(Event $event): void
	{
		$task      = $event->getTask();
		$initiator = $event->getInitiator();

		if ($task->user_id !== $initiator->id && !$task->isDone()) {
			$this->sendNotification($this->taskNotificationFactory->assigned($task, $initiator), $task->user);
		}

	}

	/**
	 * @throws InvalidConfigException
	 * @throws NotInstantiableException
	 * @throws SaveModelException
	 * @throws Throwable
	 * @throws ErrorException
	 */
	private function sendNotification(NotificationInterface $notification, NotifiableInterface $notifiable): void
	{
		$this->notifierFactory->create()
		                      ->setChannel(NotificationChannelSlugEnum::WEB)
		                      ->setNotifiable($notifiable)
		                      ->setNotification($notification)
		                      ->send();
	}
}