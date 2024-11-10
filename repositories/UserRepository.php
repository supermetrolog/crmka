<?php

declare(strict_types=1);

namespace app\repositories;

use app\models\User;

class UserRepository
{
	public function getOnlineCount(): int
	{
		return (int)User::find()->online()->count();
	}

	public function getModerator(): ?User
	{
		return User::find()->byRole(User::ROLE_MODERATOR)->one();
	}
}