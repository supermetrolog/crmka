<?php
declare(strict_types=1);

namespace app\usecases\Telegram;

use app\dto\User\UserTelegramLinkDto;
use app\helpers\DateTimeHelper;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\User\UserTelegramLink;

final class UserTelegramLinkService
{
	/**
	 * @throws SaveModelException
	 */
	public function create(UserTelegramLinkDto $dto): UserTelegramLink
	{
		$ticket = new UserTelegramLink();

		$ticket->user_id          = $dto->userId;
		$ticket->telegram_user_id = $dto->telegramUserId;
		$ticket->chat_id          = $dto->chatId;
		$ticket->username         = $dto->username;
		$ticket->first_name       = $dto->firstName;
		$ticket->last_name        = $dto->lastName;
		$ticket->is_enabled       = $dto->isEnabled;

		$ticket->saveOrThrow();

		return $ticket;
	}

	/**
	 * @throws SaveModelException
	 */
	public function disable(UserTelegramLink $link): void
	{
		$link->is_enabled = false;

		$link->saveOrThrow();
	}

	/**
	 * @throws SaveModelException
	 */
	public function enable(UserTelegramLink $link): void
	{
		$link->is_enabled = true;

		$link->saveOrThrow();
	}

	/**
	 * @throws SaveModelException
	 */
	public function revoke(UserTelegramLink $link): void
	{
		if ($link->isRevoked()) {
			return;
		}

		$link->revoked_at = DateTimeHelper::nowf();

		$link->saveOrThrow();
	}
}
