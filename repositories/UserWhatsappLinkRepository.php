<?php

declare(strict_types=1);

namespace app\repositories;

use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\kernel\common\repository\AbstractRepository;
use app\models\User\UserWhatsappLink;

class UserWhatsappLinkRepository extends AbstractRepository
{
	public function findOne(int $id): ?UserWhatsappLink
	{
		return UserWhatsappLink::find()->byId($id)->one();
	}

	/**
	 * @throws ModelNotFoundException
	 */
	public function findOneOrThrow(int $id): UserWhatsappLink
	{
		return UserWhatsappLink::find()->byId($id)->oneOrThrow();
	}

	/** @return UserWhatsappLink[] */
	public function findAll(): array
	{
		return UserWhatsappLink::find()->all();
	}

	public function findActiveByUserId(int $userId): ?UserWhatsappLink
	{
		return UserWhatsappLink::find()->byUserId($userId)->notRevoked()->one();
	}


	/**
	 * @throws ModelNotFoundException
	 */
	public function findActiveByUserIdOrThrow(int $userId): UserWhatsappLink
	{
		return UserWhatsappLink::find()->byUserId($userId)->notRevoked()->oneOrThrow();
	}

	public function findActiveByWhatsappProfileId(string $profileId): ?UserWhatsappLink
	{
		return UserWhatsappLink::find()->byWhatsappProfileId($profileId)->notRevoked()->one();
	}

	/**
	 * @throws ModelNotFoundException
	 */
	public function findActiveByWhatsappProfileIdOrThrow(string $profileId): UserWhatsappLink
	{
		return UserWhatsappLink::find()->byWhatsappProfileId($profileId)->notRevoked()->oneOrThrow();
	}
}