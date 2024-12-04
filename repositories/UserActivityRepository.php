<?php

declare(strict_types=1);

namespace app\repositories;

use app\models\UserActivity;

class UserActivityRepository
{

	public function findLastActivityByUserId(int $userId): ?UserActivity
	{
		return UserActivity::find()->byUserId($userId)->orderBy(['id' => SORT_DESC])->one();
	}
}