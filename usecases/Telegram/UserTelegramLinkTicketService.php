<?php
declare(strict_types=1);

namespace app\usecases\Telegram;

use app\dto\User\UserTelegramLinkTicketDto;
use app\helpers\DateTimeHelper;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\User\UserTelegramLinkTicket;

final class UserTelegramLinkTicketService
{
	/**
	 * @throws SaveModelException
	 */
	public function create(UserTelegramLinkTicketDto $dto): UserTelegramLinkTicket
	{
		$ticket = new UserTelegramLinkTicket();

		$ticket->user_id    = $dto->userId;
		$ticket->code       = $dto->code;
		$ticket->expires_at = DateTimeHelper::format($dto->expiresAt);

		$ticket->saveOrThrow();

		return $ticket;
	}

	/**
	 * @throws SaveModelException
	 */
	public function consume(UserTelegramLinkTicket $ticket): void
	{
		if ($ticket->isConsumed()) {
			return;
		}

		$ticket->consumed_at = DateTimeHelper::nowf();

		$ticket->saveOrThrow();
	}
}
