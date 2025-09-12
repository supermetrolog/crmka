<?php

namespace app\listeners\Request;

use app\components\Notification\Factories\NotifierFactory;
use app\components\Notification\Interfaces\NotifiableInterface;
use app\components\Notification\Interfaces\NotificationInterface;
use app\enum\Notification\NotificationChannelSlugEnum;
use app\events\Request\RequestConsultantChangedEvent;
use app\factories\Notification\RequestNotificationFactory;
use app\kernel\common\models\exceptions\SaveModelException;
use app\listeners\EventListenerInterface;
use ErrorException;
use Throwable;
use yii\base\Event;
use yii\base\InvalidConfigException;
use yii\di\NotInstantiableException;


class NotifyAssignedConsultantOnRequestConsultantChangedListener implements EventListenerInterface
{
	private NotifierFactory            $notifierFactory;
	private RequestNotificationFactory $requestNotificationFactory;

	public function __construct(NotifierFactory $notifierFactory, RequestNotificationFactory $requestNotificationFactory)
	{
		$this->notifierFactory            = $notifierFactory;
		$this->requestNotificationFactory = $requestNotificationFactory;
	}

	/**
	 * @param RequestConsultantChangedEvent $event
	 *
	 * @throws Throwable
	 */
	public function handle(Event $event): void
	{
		$request = $event->getRequest();

		$consultant = $event->getNewConsultant();

		$this->sendNotification($this->requestNotificationFactory->assigned($request), $consultant);
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