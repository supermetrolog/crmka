<?php

namespace app\listeners\Company;

use app\dto\ChatMember\CreateChatMemberSystemMessageDto;
use app\dto\Notification\CreateNotificationDto;
use app\events\Company\DisableCompanyEvent;
use app\helpers\StringHelper;
use app\kernel\common\database\interfaces\transaction\TransactionBeginnerInterface;
use app\kernel\common\models\exceptions\SaveModelException;
use app\listeners\EventListenerInterface;
use app\models\ChatMember;
use app\models\ChatMemberMessage;
use app\models\Company;
use app\models\Notification\NotificationChannel;
use app\models\User;
use app\usecases\ChatMember\ChatMemberMessageService;
use Throwable;
use yii\base\Event;
use yii\db\Exception;


class DisableCompanySystemChatMemberMessageListener implements EventListenerInterface
{
	private ChatMemberMessageService     $chatMemberMessageService;
	private TransactionBeginnerInterface $transactionBeginner;

	public function __construct(ChatMemberMessageService $chatMemberMessageService, TransactionBeginnerInterface $transactionBeginner)
	{
		$this->chatMemberMessageService = $chatMemberMessageService;
		$this->transactionBeginner      = $transactionBeginner;
	}

	/**
	 * @param DisableCompanyEvent $event
	 *
	 * @throws Throwable
	 * @throws SaveModelException
	 */
	public function handle(Event $event): void
	{
		$company           = $event->getCompany();
		$companyChatMember = $company->chatMember;

		if (!$companyChatMember) {
			return;
		}

		$tx = $this->transactionBeginner->begin();

		try {
			$systemMessage = $this->createSystemMessage($companyChatMember, $company, $event->getInitiator());

			if ($this->notificationShouldBeCreated($event)) {
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
	private function createSystemMessage(ChatMember $chatMember, Company $company, ?User $initiator): ChatMemberMessage
	{
		$message = sprintf(
			'Компания архивирована. Причина: %s. Комментарий: %s',
			Company::resolvePassiveWhyOption($company->passive_why),
			$company->passive_why_comment ?? 'Отсутствует'
		);

		if ($initiator) {
			$message = StringHelper::join('. ', $message, sprintf('Ответственный: %s', $initiator->userProfile->getMediumName()));
		}

		$dto = new CreateChatMemberSystemMessageDto([
			'message' => $message,
			'to'      => $chatMember
		]);


		return $this->chatMemberMessageService->createSystemMessage($dto);
	}

	/**
	 * @param DisableCompanyEvent $event
	 */
	private function notificationShouldBeCreated(Event $event): bool
	{
		$initiator = $event->getInitiator();

		if (!$initiator) {
			return true;
		}

		return $initiator->id !== $event->getCompany()->consultant_id;
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
			'subject'         => 'Архивация прикрепленной компании',
			'message'         => sprintf('Компания %s архивирована и больше не является активной', $company->getShortName()),
			'notifiable'      => $company->consultant,
			'created_by_id'   => $chatMemberMessage->fromChatMember->model_id,
			'created_by_type' => $chatMemberMessage->fromChatMember::getMorphClass()
		]));
	}
}