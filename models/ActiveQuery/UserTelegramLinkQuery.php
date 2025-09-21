<?php

declare(strict_types=1);

namespace app\models\ActiveQuery;

use app\kernel\common\models\AQ\AQ;
use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\models\User\UserTelegramLink;

class UserTelegramLinkQuery extends AQ
{
	public function one($db = null): ?UserTelegramLink
	{
		/** @var UserTelegramLink */
		return parent::one($db);
	}

	/**
	 * @throws ModelNotFoundException
	 */
	public function oneOrThrow($db = null): UserTelegramLink
	{
		/** @var UserTelegramLink */
		return parent::oneOrThrow($db);
	}

	/**
	 * @return UserTelegramLink[]
	 */
	public function all($db = null): array
	{
		return parent::all($db);
	}

	public function revoked(): self
	{
		return $this->andWhereNotNull('revoked_at');
	}

	public function notRevoked(): self
	{
		return $this->andWhereNull('revoked_at');
	}

	public function enabled(): self
	{
		return $this->andWhere(['is_enabled' => true])->notRevoked();
	}

	public function disabled(): self
	{
		return $this->andWhere(['is_enabled' => false]);
	}

	public function byUserId(int $userId): self
	{
		return $this->andWhere(['user_id' => $userId]);
	}

	public function byTelegramUserId(int $telegramUserId): self
	{
		return $this->andWhere(['telegram_user_id' => $telegramUserId]);
	}
}