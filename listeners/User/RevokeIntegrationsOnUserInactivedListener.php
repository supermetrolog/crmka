<?php

namespace app\listeners\User;

use app\events\User\UserArchivedEvent;
use app\events\User\UserDeletedEvent;
use app\kernel\common\database\interfaces\transaction\TransactionBeginnerInterface;
use app\listeners\EventListenerInterface;
use app\models\User\User;
use app\usecases\Telegram\TelegramLinkService;
use app\usecases\Whatsapp\WhatsappLinkService;
use Throwable;
use yii\base\Event;


class RevokeIntegrationsOnUserInactivedListener implements EventListenerInterface
{
	private TelegramLinkService          $telegramLinkService;
	private WhatsappLinkService          $whatsappLinkService;
	private TransactionBeginnerInterface $transactionBeginner;

	public function __construct(
		TelegramLinkService $telegramLinkService,
		WhatsappLinkService $whatsappLinkService,
		TransactionBeginnerInterface $transactionBeginner
	)
	{
		$this->telegramLinkService = $telegramLinkService;
		$this->whatsappLinkService = $whatsappLinkService;
		$this->transactionBeginner = $transactionBeginner;
	}

	/**
	 * @param UserArchivedEvent|UserDeletedEvent $event
	 *
	 * @throws Throwable
	 */
	public function handle(Event $event): void
	{
		$user = $event->getUser();

		if ($user->isActive()) {
			return;
		}

		$this->revokeIntegrations($event->getUser());
	}

	private function revokeIntegrations(User $user): void
	{
		$this->transactionBeginner->run(function () use ($user) {
			$this->whatsappLinkService->revokeByUser($user);
			$this->telegramLinkService->revokeByUser($user);
		});
	}
}