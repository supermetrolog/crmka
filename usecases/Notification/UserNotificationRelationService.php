<?php

declare(strict_types=1);

namespace app\usecases\Notification;

use app\dto\UserNotification\UserNotificationRelationDto;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\Notification\UserNotificationRelation;

class UserNotificationRelationService
{
	/**
	 * @throws SaveModelException
	 */
	public function create(UserNotificationRelationDto $dto): UserNotificationRelation
	{
		$action = new UserNotificationRelation();

		$action->notification_id = $dto->notificationId;
		$action->entity_type     = $dto->entityType;
		$action->entity_id       = $dto->entityId;

		$action->saveOrThrow();

		return $action;
	}
}