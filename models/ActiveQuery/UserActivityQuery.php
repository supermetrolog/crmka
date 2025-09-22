<?php

namespace app\models\ActiveQuery;

use app\kernel\common\models\AQ\AQ;
use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\models\User\UserActivity;

class UserActivityQuery extends AQ
{
	/**
	 * @return UserActivity[]
	 */
	public function all($db = null): array
	{
		return parent::all($db);
	}

	public function one($db = null): ?UserActivity
	{
		/** @var ?UserActivity */
		return parent::one($db);
	}

	/**
	 * @throws ModelNotFoundException
	 */
	public function oneOrThrow($db = null): UserActivity
	{
		/** @var UserActivity */
		return parent::oneOrThrow($db);
	}

	public function byUserId(int $userId): self
	{
		return $this->andWhere(['user_id' => $userId]);
	}

	public function today(): self
	{
		return $this->andWhere(['>=', 'started_at', date('Y-m-d 00:00:00')]);
	}

	public function before(string $date): self
	{
		return $this->andWhere(['<=', $this->field('last_activity_at'), $date]);
	}

	public function after(string $date): self
	{
		return $this->andWhere(['>=', $this->field('started_at'), $date]);
	}

	public function between(string $from, string $to): self
	{
		return $this->before($to)->after($from);
	}
}
