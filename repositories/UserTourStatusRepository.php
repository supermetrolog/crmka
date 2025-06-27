<?php

declare(strict_types=1);

namespace app\repositories;

use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\models\UserTourStatus;

class UserTourStatusRepository
{
	public function findOneByUserIdAndTourId(int $userId, string $tourId): ?UserTourStatus
	{
		return UserTourStatus::find()->byUserId($userId)->byTourId($tourId)->one();
	}

	/**
	 * @throws ModelNotFoundException
	 */
	public function findOneByIdOrThrow(int $id): UserTourStatus
	{
		return UserTourStatus::find()->byId($id)->oneOrThrow();
	}
}