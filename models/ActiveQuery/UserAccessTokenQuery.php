<?php

namespace app\models\ActiveQuery;

use app\kernel\common\models\AQ\AQ;
use app\kernel\common\models\AQ\SoftDeleteTrait;
use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\models\UserAccessToken;
use yii\db\ActiveRecord;
use yii\db\Expression;

class UserAccessTokenQuery extends AQ
{
	use SoftDeleteTrait;

	/**
	 * @return UserAccessToken[]|ActiveRecord[
	 */
	public function all($db = null): array
	{
		return parent::all($db);
	}

	/**
	 * @return null|UserAccessToken|ActiveRecord
	 */
	public function one($db = null): ?UserAccessToken
	{
		return parent::one($db);
	}

	/**
	 * @return UserAccessToken|ActiveRecord|null
	 * @throws ModelNotFoundException
	 */
	public function oneOrThrow($db = null): UserAccessToken
	{
		return parent::oneOrThrow($db);
	}

	/**
	 * @return UserAccessTokenQuery
	 */
	public function notExpired(): self
	{
		return $this->andWhere(
			['>', $this->field('expires_at'), new Expression('NOW()')]
		);
	}

	/**
	 * @param string $token
	 *
	 * @return UserAccessTokenQuery
	 */
	public function byToken(string $token): self
	{
		return $this->andWhere(['access_token' => $token]);
	}

	/**
	 * @param int $userId
	 *
	 * @return UserAccessTokenQuery
	 */
	public function byUserId(int $userId): self
	{
		return $this->andWhere(['user_id' => $userId]);
	}
}
