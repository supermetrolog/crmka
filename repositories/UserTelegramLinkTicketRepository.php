<?php

declare(strict_types=1);

namespace app\repositories;

use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\kernel\common\repository\AbstractRepository;
use app\models\User\UserTelegramLink;
use app\models\User\UserTelegramLinkTicket;

class UserTelegramLinkTicketRepository extends AbstractRepository
{
	public function findOne(int $id): ?UserTelegramLinkTicket
	{
		return UserTelegramLinkTicket::find()->byId($id)->one();
	}

	/**
	 * @throws ModelNotFoundException
	 */
	public function findOneOrThrow(int $id): UserTelegramLinkTicket
	{
		return UserTelegramLinkTicket::find()->byId($id)->oneOrThrow();
	}

	/** @return UserTelegramLink[] */
	public function findAll(): array
	{
		return UserTelegramLinkTicket::find()->all();
	}

	/**
	 * @throws ModelNotFoundException
	 */
	public function findByCodeOrThrow(string $code): UserTelegramLinkTicket
	{
		return UserTelegramLinkTicket::find()->byCode($code)->oneOrThrow();
	}

	public function findActiveByUserId(int $userId): ?UserTelegramLinkTicket
	{
		return UserTelegramLinkTicket::find()->byUserId($userId)->active()->one();
	}
}