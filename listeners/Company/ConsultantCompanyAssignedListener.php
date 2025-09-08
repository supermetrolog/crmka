<?php

namespace app\listeners\Company;

use app\components\Notification\Factories\CompanyNotificationFactory;
use app\components\Notification\Factories\NotifierFactory;
use app\components\Notification\Interfaces\NotifiableInterface;
use app\components\Notification\Interfaces\NotificationInterface;
use app\enum\Notification\NotificationChannelSlugEnum;
use app\events\Company\ConsultantCompanyAssignedEvent;
use app\kernel\common\models\exceptions\SaveModelException;
use app\listeners\EventListenerInterface;
use ErrorException;
use Throwable;
use yii\base\Event;
use yii\base\InvalidConfigException;
use yii\di\NotInstantiableException;


class ConsultantCompanyAssignedListener implements EventListenerInterface
{
	private NotifierFactory            $notifierFactory;
	private CompanyNotificationFactory $companyNotificationFactory;

	public function __construct(NotifierFactory $notifierFactory, CompanyNotificationFactory $companyNotificationFactory)
	{
		$this->notifierFactory            = $notifierFactory;
		$this->companyNotificationFactory = $companyNotificationFactory;
	}

	/**
	 * @param ConsultantCompanyAssignedEvent $event
	 *
	 * @throws Throwable
	 */
	public function handle(Event $event): void
	{
		$company    = $event->getCompany();
		$consultant = $event->getConsultant();

		$this->sendNotification($this->companyNotificationFactory->assigned($company), $consultant);
	}

	/**
	 * @throws SaveModelException
	 * @throws Throwable
	 * @throws ErrorException
	 * @throws InvalidConfigException
	 * @throws NotInstantiableException
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