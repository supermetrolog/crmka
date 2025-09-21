<?php

namespace app\listeners\Company;

use app\components\Notification\Factories\NotifierFactory;
use app\components\Notification\Interfaces\NotifiableInterface;
use app\components\Notification\Interfaces\NotificationInterface;
use app\dto\ChatMember\CreateChatMemberSystemMessageDto;
use app\enum\Notification\NotificationChannelSlugEnum;
use app\events\Company\ChangeConsultantCompanyEvent;
use app\factories\Notification\CompanyNotificationFactory;
use app\kernel\common\database\interfaces\transaction\TransactionBeginnerInterface;
use app\kernel\common\models\exceptions\SaveModelException;
use app\listeners\EventListenerInterface;
use app\models\ChatMember;
use app\models\Company\Company;
use app\models\User\User;
use app\services\ChatMemberSystemMessage\ChangeConsultantCompanyChatMemberSystemMessage;
use app\usecases\ChatMember\ChatMemberMessageService;
use ErrorException;
use Throwable;
use yii\base\Event;
use yii\base\InvalidConfigException;
use yii\di\NotInstantiableException;


class ChangeConsultantCompanySystemChatMessageListener implements EventListenerInterface
{
	private ChatMemberMessageService     $chatMemberMessageService;
	private TransactionBeginnerInterface $transactionBeginner;
	private NotifierFactory              $notifierFactory;
	private CompanyNotificationFactory   $companyNotificationFactory;

	public function __construct(ChatMemberMessageService $chatMemberMessageService, TransactionBeginnerInterface $transactionBeginner, NotifierFactory $notifierFactory, CompanyNotificationFactory $companyNotificationFactory)
	{
		$this->chatMemberMessageService   = $chatMemberMessageService;
		$this->transactionBeginner        = $transactionBeginner;
		$this->notifierFactory            = $notifierFactory;
		$this->companyNotificationFactory = $companyNotificationFactory;
	}

	/**
	 * @param ChangeConsultantCompanyEvent $event
	 *
	 * @throws Throwable
	 * @throws SaveModelException
	 */
	public function handle(Event $event): void
	{
		$tx = $this->transactionBeginner->begin();

		try {
			$company       = $event->getCompany();
			$oldConsultant = $event->getOldConsultant();
			$newConsultant = $event->getNewConsultant();

			if ($company->chatMember) {
				$this->createSystemMessage($company->chatMember, $oldConsultant, $newConsultant);
			}

			$this->createNotifications($company, $oldConsultant, $newConsultant);

			$tx->commit();
		} catch (Throwable $e) {
			$tx->rollBack();
			throw $e;
		}
	}

	/**
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	private function createSystemMessage(ChatMember $chatMember, User $oldConsultant, User $newConsultant): void
	{
		$message = ChangeConsultantCompanyChatMemberSystemMessage::create()
		                                                         ->setConsultant($newConsultant)
		                                                         ->setOldConsultant($oldConsultant)
		                                                         ->toMessage();

		$this->chatMemberMessageService->createSystemMessage(
			new CreateChatMemberSystemMessageDto([
				'message' => $message,
				'to'      => $chatMember
			])
		);
	}

	/**
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	private function createNotifications(Company $company, User $oldConsultant, User $newConsultant): void
	{
		$tx = $this->transactionBeginner->begin();

		try {
			$this->sendNotification($this->companyNotificationFactory->assigned($company), $newConsultant);

			$this->sendNotification($this->companyNotificationFactory->reassigned($company), $oldConsultant);

			$tx->commit();
		} catch (Throwable $th) {
			$tx->rollBack();
			throw $th;
		}
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