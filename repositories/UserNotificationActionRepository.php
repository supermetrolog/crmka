<?php

declare(strict_types=1);

namespace app\repositories;

use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\kernel\common\repository\AbstractRepository;
use app\models\Notification\UserNotificationAction;

class UserNotificationActionRepository extends AbstractRepository
{
	public function findOne(int $id): ?UserNotificationAction
	{
		/** @var ?UserNotificationAction */
		return UserNotificationAction::find()->byId($id)->one();
	}

	/**
	 * @throws ModelNotFoundException
	 */
	public function findOneOrThrow(int $id): UserNotificationAction
	{
		/** @var UserNotificationAction */
		return UserNotificationAction::find()->byId($id)->oneOrThrow();
	}

	/**
	 * @return UserNotificationAction[]
	 */
	public function findAll(): array
	{
		return UserNotificationAction::find()->all();
	}
}