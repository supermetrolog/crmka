<?php

declare(strict_types=1);

namespace app\models\ActiveQuery;

use app\kernel\common\models\AQ\AQ;
use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\models\User\UserWhatsappLink;

class UserWhatsappLinkQuery extends AQ
{
	public function one($db = null): ?UserWhatsappLink
	{
		/** @var UserWhatsappLink */
		return parent::one($db);
	}

	/**
	 * @throws ModelNotFoundException
	 */
	public function oneOrThrow($db = null): UserWhatsappLink
	{
		/** @var UserWhatsappLink */
		return parent::oneOrThrow($db);
	}

	/**
	 * @return UserWhatsappLink[]
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

	public function byUserId(int $userId): self
	{
		return $this->andWhere(['user_id' => $userId]);
	}

	public function byWhatsappProfileId(string $profileId): self
	{
		return $this->andWhere(['whatsapp_profile_id' => $profileId]);
	}
}