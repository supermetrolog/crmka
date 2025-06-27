<?php

declare(strict_types=1);

namespace app\repositories;

use app\models\UserTourView;

class UserTourViewRepository
{
	public function findOneByUserIdAndTourId(int $userId, $tourId): UserTourView
	{
		return UserTourView::find()->byUserId($userId)->byTourId($tourId)->one();
	}
}