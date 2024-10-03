<?php

declare(strict_types=1);

namespace app\models\ActiveQuery;

use app\kernel\common\models\AQ\AQ;
use app\models\User;
use yii\db\ActiveRecord;

class UserQuery extends AQ
{
	/**
	 * @param $db
	 *
	 * @return array|ActiveRecord|User|null
	 */
	public function one($db = null): ?User
	{
		return parent::one($db);
	}

	/**
	 * @param $db
	 *
	 * @return array|User[]
	 */
	public function all($db = null): array
	{
		return parent::all($db);
	}

	public function byAccessToken(string $token): self
	{
		$this->innerJoinWith(['userAccessTokens' => function (UserAccessTokenQuery $query) use ($token) {
			$query->valid()->byToken($token);
		}]);

		return $this;
	}

	public function byUsername(string $username): self
	{
		return $this->andWhere(['username' => $username]);
	}
}