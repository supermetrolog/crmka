<?php

declare(strict_types=1);

namespace app\models\ActiveQuery;

use app\helpers\ArrayHelper;
use app\helpers\DateTimeHelper;
use app\helpers\SQLHelper;
use app\kernel\common\models\AQ\AQ;
use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\models\User\User;
use yii\base\InvalidArgumentException;
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
	 * @return User|ActiveRecord
	 * @throws ModelNotFoundException
	 */
	public function oneOrThrow($db = null): User
	{
		return parent::oneOrThrow($db);
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

	public function system(): self
	{
		return $this->byRole(User::ROLE_SYSTEM);
	}

	public function byUsername(string $username): self
	{
		return $this->andWhere(['username' => $username]);
	}

	public function byRole(int $role): self
	{
		if (!ArrayHelper::includes(User::getRoles(), $role)) {
			throw new InvalidArgumentException('Invalid user role');
		}

		return $this->andWhere(['role' => $role]);
	}

	public function online(): self
	{
		return $this->andWhere([
				'>=',
				SQLHelper::toUnixTime($this->field('last_seen')),
				DateTimeHelper::unix() - User::ACTIVITY_TIMEOUT]
		);
	}

	public function byStatus(int $status): self
	{
		return $this->andWhere([$this->field('status') => $status]);
	}

	public function notDeleted(): self
	{
		return $this->andWhere(['!=', $this->field('status'), User::STATUS_DELETED]);
	}
}