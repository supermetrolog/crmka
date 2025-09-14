<?php

namespace app\listeners\Task;

use app\components\Notification\Factories\NotifierFactory;
use app\components\Notification\Interfaces\NotifiableInterface;
use app\components\Notification\Interfaces\NotificationInterface;
use app\enum\Notification\NotificationChannelSlugEnum;
use app\events\Task\TaskCommentCreatedEvent;
use app\factories\Notification\TaskCommentNotificationFactory;
use app\kernel\common\database\interfaces\transaction\TransactionBeginnerInterface;
use app\kernel\common\models\exceptions\SaveModelException;
use app\listeners\EventListenerInterface;
use app\models\TaskComment;
use ErrorException;
use Throwable;
use yii\base\Event;
use yii\base\InvalidConfigException;
use yii\di\NotInstantiableException;


class NotifyUsersOnTaskCommentCreatedListener implements EventListenerInterface
{
	private NotifierFactory                $notifierFactory;
	private TaskCommentNotificationFactory $taskCommentNotificationFactory;
	private TransactionBeginnerInterface   $transactionBeginner;

	public function __construct(NotifierFactory $notifierFactory, TaskCommentNotificationFactory $taskCommentNotificationFactory, TransactionBeginnerInterface $transactionBeginner)
	{
		$this->notifierFactory                = $notifierFactory;
		$this->taskCommentNotificationFactory = $taskCommentNotificationFactory;
		$this->transactionBeginner            = $transactionBeginner;
	}

	/**
	 * @param TaskCommentCreatedEvent $event
	 *
	 * @throws Throwable
	 */
	public function handle(Event $event): void
	{
		$this->sendNotifications($event->getTaskComment());
	}

	/**
	 * @throws ErrorException
	 * @throws InvalidConfigException
	 * @throws NotInstantiableException
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	private function sendNotifications(TaskComment $comment): void
	{
		$tx = $this->transactionBeginner->begin();

		try {
			$author = $comment->createdBy;

			$notification = $this->taskCommentNotificationFactory->created($comment);

			if ($author->id !== $comment->task->created_by_id) {
				$this->sendNotification($notification, $comment->task->createdBy);
			}

			if ($author->id !== $comment->task->user_id) {
				$this->sendNotification($notification, $comment->task->user);
			}

			$tx->commit();
		} catch (Throwable $e) {
			$tx->rollBack();
			throw $e;
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