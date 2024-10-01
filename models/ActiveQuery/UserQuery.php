<?php

declare(strict_types=1);

namespace app\models\ActiveQuery;

use app\kernel\common\models\AQ\AQ;
use app\models\User;
use app\models\UserAccessToken;
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
		$userAccessToken = UserAccessToken::findValid()->byToken($token);

		return $this->leftJoin(['uat' => $userAccessToken], ['uat.user_id' => $this->field('id')]);
	}
}