<?php

declare(strict_types=1);

namespace app\repositories;

use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\kernel\common\repository\AbstractRepository;
use app\models\User\UserTelegramLink;

class UserTelegramLinkRepository extends AbstractRepository
{
	public function findOne(int $id): ?UserTelegramLink
	{
		return UserTelegramLink::find()->byId($id)->one();
	}

	/**
	 * @throws ModelNotFoundException
	 */
	public function findOneOrThrow(int $id): UserTelegramLink
	{
		return UserTelegramLink::find()->byId($id)->oneOrThrow();
	}

	/** @return UserTelegramLink[] */
	public function findAll(): array
	{
		return UserTelegramLink::find()->all();
	}

	public function findActiveByUserId(int $userId): ?UserTelegramLink
	{
		return UserTelegramLink::find()->byUserId($userId)->notRevoked()->one();
	}


	/**
	 * @throws ModelNotFoundException
	 */
	public function findActiveByUserIdOrThrow(int $userId): UserTelegramLink
	{
		return UserTelegramLink::find()->byUserId($userId)->notRevoked()->oneOrThrow();
	}

	public function findActiveByTelegramUserId(int $telegramUserId): ?UserTelegramLink
	{
		return UserTelegramLink::find()->byTelegramUserId($telegramUserId)->notRevoked()->one();
	}

	/**
	 * @throws ModelNotFoundException
	 */
	public function findActiveByTelegramUserIdOrThrow(int $telegramUserId): UserTelegramLink
	{
		return UserTelegramLink::find()->byTelegramUserId($telegramUserId)->notRevoked()->oneOrThrow();
	}
}