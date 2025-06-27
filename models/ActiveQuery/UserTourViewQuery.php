<?php

declare(strict_types=1);

namespace app\models\ActiveQuery;

use app\kernel\common\models\AQ\AQ;
use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\models\UserTourView;

class UserTourViewQuery extends AQ
{
	public function one($db = null): ?UserTourView
	{
		/** @var UserTourView */
		return parent::one($db);
	}

	/**
	 * @throws ModelNotFoundException
	 */
	public function oneOrThrow($db = null): UserTourView
	{
		/** @var UserTourView */
		return parent::oneOrThrow($db);
	}

	/**
	 * @return UserTourView[]
	 */
	public function all($db = null): array
	{
		return parent::all($db);
	}

	public function byUserId(int $userId): self
	{
		return $this->andWhere(['user_id' => $userId]);
	}

	public function byTourId(string $tourId): self
	{
		return $this->andWhere(['tour_id' => $tourId]);
	}
}