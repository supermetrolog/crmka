<?php

namespace app\listeners\Company;

use app\dto\ChatMember\CreateChatMemberSystemMessageDto;
use app\dto\Notification\CreateNotificationDto;
use app\events\Company\ChangeConsultantCompanyEvent;
use app\kernel\common\database\interfaces\transaction\TransactionBeginnerInterface;
use app\kernel\common\models\exceptions\SaveModelException;
use app\listeners\EventListenerInterface;
use app\models\ChatMember;
use app\models\ChatMemberMessage;
use app\models\Company;
use app\models\Notification\NotificationChannel;
use app\models\User;
use app\services\ChatMemberSystemMessage\ChangeConsultantCompanyChatMemberSystemMessage;
use app\usecases\ChatMember\ChatMemberMessageService;
use Throwable;
use yii\base\Event;
use yii\db\Exception;


class ChangeConsultantCompanySystemChatMessageListener implements EventListenerInterface
{
	private ChatMemberMessageService     $chatMemberMessageService;
	private TransactionBeginnerInterface $transactionBeginner;

	public function __construct(ChatMemberMessageService $chatMemberMessageService, TransactionBeginnerInterface $transactionBeginner)
	{
		$this->chatMemberMessageService = $chatMemberMessageService;
		$this->transactionBeginner      = $transactionBeginner;
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
			$company           = $event->getCompany();
			$newConsultant     = $event->getNewConsultant();
			$oldConsultant     = $event->getOldConsultant();
			$companyChatMember = $company->chatMember;

			if ($companyChatMember) {
				$systemMessage = $this->createSystemMessage($companyChatMember, $oldConsultant, $newConsultant);
				$this->createNotification($systemMessage, $company);
			}

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
	private function createSystemMessage(ChatMember $chatMember, User $oldConsultant, User $newConsultant): ChatMemberMessage
	{
		$message = ChangeConsultantCompanyChatMemberSystemMessage::create()
		                                                         ->setConsultant($newConsultant)
		                                                         ->setOldConsultant($oldConsultant)
		                                                         ->toMessage();

		$dto = new CreateChatMemberSystemMessageDto([
			'message' => $message,
			'to'      => $chatMember
		]);


		return $this->chatMemberMessageService->createSystemMessage($dto);
	}

	/**
	 * @throws SaveModelException
	 * @throws Throwable
	 * @throws Exception
	 */
	private function createNotification(ChatMemberMessage $chatMemberMessage, Company $company): void
	{
		$this->chatMemberMessageService->createNotification($chatMemberMessage, new CreateNotificationDto([
			'channel'         => NotificationChannel::WEB,
			'subject'         => 'Назначение консультантом компании',
			'message'         => sprintf('Вы назначены консультантом для компании %s', $company->getFullName()),
			'notifiable'      => $company->consultant,
			'created_by_id'   => $chatMemberMessage->fromChatMember->model_id,
			'created_by_type' => $chatMemberMessage->fromChatMember::getMorphClass()
		]));
	}
}