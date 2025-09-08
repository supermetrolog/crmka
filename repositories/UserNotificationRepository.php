<?php

declare(strict_types=1);

namespace app\repositories;

use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\kernel\common\repository\AbstractRepository;
use app\models\Notification\UserNotification;
use yii\base\ErrorException;

class UserNotificationRepository extends AbstractRepository
{
	public function findOne(int $id): ?UserNotification
	{
		/** @var ?UserNotification */
		return UserNotification::find()->byId($id)->one();
	}

	/**
	 * @throws ModelNotFoundException
	 */
	public function findOneOrThrow(int $id): UserNotification
	{
		return UserNotification::find()->byId($id)->oneOrThrow();
	}

	/**
	 * @throws ModelNotFoundException
	 */
	public function findOneOrThrowWithRelations(int $id): UserNotification
	{
		return UserNotification::find()->with(['mailing.createdByUser.userProfile', 'userNotificationActions.userNotificationActionLogs'])
		                       ->byId($id)
		                       ->oneOrThrow();
	}

	/**
	 * @return UserNotification[]
	 */
	public function findAll(): array
	{
		return UserNotification::find()->all();
	}

	/**
	 * @throws ErrorException
	 */
	public function countByUserId(int $userId): int
	{
		return (int)UserNotification::find()->notActed()->notExpired()->byUserId($userId)->count();
	}
}