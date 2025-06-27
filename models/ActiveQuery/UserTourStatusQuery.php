<?php

declare(strict_types=1);

namespace app\models\ActiveQuery;

use app\kernel\common\models\AQ\AQ;
use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\models\UserTourStatus;

class UserTourStatusQuery extends AQ
{
	public function one($db = null): ?UserTourStatus
	{
		/** @var UserTourStatus */
		return parent::one($db);
	}

	/**
	 * @throws ModelNotFoundException
	 */
	public function oneOrThrow($db = null): UserTourStatus
	{
		/** @var UserTourStatus */
		return parent::oneOrThrow($db);
	}

	/**
	 * @return UserTourStatus[]
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

	public function seen(): self
	{
		return $this->andWhere(['seen' => true]);
	}
}