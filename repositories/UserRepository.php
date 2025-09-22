<?php

declare(strict_types=1);

namespace app\repositories;

use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\kernel\common\repository\AbstractRepository;
use app\models\User\User;

class UserRepository extends AbstractRepository
{
	public function getOnlineCount(): int
	{
		return (int)User::find()->notDeleted()->online()->count();
	}

	public function getModerator(): ?User
	{
		return User::find()->byRole(User::ROLE_MODERATOR)->notDeleted()->one();
	}

	/**
	 * @throws ModelNotFoundException
	 */
	public function getModeratorOrThrow(): ?User
	{
		try {
			return User::find()->byRole(User::ROLE_MODERATOR)->notDeleted()->oneOrThrow();
		} catch (ModelNotFoundException $e) {
			throw new ModelNotFoundException('Moderator not found');
		}
	}

	public function findOne(int $id): ?User
	{
		return User::find()->byId($id)->one();
	}

	/**
	 * @throws ModelNotFoundException
	 */
	public function findOneOrThrow(int $id): User
	{
		return User::find()->byId($id)->oneOrThrow();
	}

	public function findAll(): array
	{
		return User::find()->all();
	}

	/**
	 * @throws ModelNotFoundException
	 */
	public function getByUsernameOrThrow(string $username): User
	{
		return User::find()->byUsername($username)->notDeleted()->oneOrThrow();
	}

	public function getByUsername(string $username): ?User
	{
		return User::find()->byUsername($username)->notDeleted()->one();
	}
}