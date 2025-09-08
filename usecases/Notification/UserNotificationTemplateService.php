<?php

declare(strict_types=1);

namespace app\usecases\Notification;

use app\dto\UserNotification\CreateUserNotificationDto;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\Notification\UserNotification;
use app\models\Notification\UserNotificationTemplate;

class UserNotificationTemplateService
{

	/**
	 * @throws SaveModelException
	 */
	public function create(CreateUserNotificationDto $dto): UserNotification
	{
		$model = new UserNotificationTemplate();

		$model->saveOrThrow();

		return $model;
	}
}