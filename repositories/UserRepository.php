<?php

declare(strict_types=1);

namespace app\repositories;

use app\kernel\common\models\exceptions\ModelNotFoundException;
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

	/**
	 * @throws ModelNotFoundException
	 */
	public function getModeratorOrThrow(): ?User
	{
		try {
			return User::find()->byRole(User::ROLE_MODERATOR)->oneOrThrow();
		} catch (ModelNotFoundException $e) {
			throw new ModelNotFoundException('Moderator not found');
		}
	}

	public function findOne(int $id): ?User
	{
		return User::find()->byId($id)->one();
	}
}