<?php

declare(strict_types=1);

namespace app\repositories;

use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\kernel\common\repository\AbstractRepository;
use app\models\Notification\UserNotificationTemplate;

class UserNotificationTemplateRepository extends AbstractRepository
{
	public function findOne(int $id): ?UserNotificationTemplate
	{
		/** @var ?UserNotificationTemplate */
		return UserNotificationTemplate::find()->byId($id)->one();
	}

	/**
	 * @throws ModelNotFoundException
	 */
	public function findOneOrThrow(int $id): UserNotificationTemplate
	{
		/** @var UserNotificationTemplate */
		return UserNotificationTemplate::find()->byId($id)->oneOrThrow();
	}

	/**
	 * @return UserNotificationTemplate[]
	 */
	public function findAll(): array
	{
		return UserNotificationTemplate::find()->all();
	}

	public function findOneByKind(string $kind): ?UserNotificationTemplate
	{
		/** @var ?UserNotificationTemplate */
		return UserNotificationTemplate::find()->andWhere(['kind' => $kind])->one();
	}
}