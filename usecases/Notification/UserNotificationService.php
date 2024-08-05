<?php

declare(strict_types=1);

namespace app\usecases\Notification;

use app\dto\UserNotification\CreateUserNotificationDto;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\Notification\UserNotification;

class UserNotificationService
{

	/**
	 * @throws SaveModelException
	 */
	public function create(CreateUserNotificationDto $dto): UserNotification
	{
		$model = new UserNotification();

		$model->mailing_id  = $dto->mailing_id;
		$model->user_id     = $dto->user_id;
		$model->notified_at = $dto->notified_at ? $dto->notified_at->format('Y-m-d H:i:s') : null;
		$model->viewed_at   = $dto->viewed_at ? $dto->viewed_at->format('Y-m-d H:i:s') : null;

		$model->saveOrThrow();

		return $model;
	}

	/**
	 * @throws SaveModelException
	 */
	public function viewed(UserNotification $notification): void
	{
		$notification->viewed_at = (new \DateTime())->format('Y-m-d H:i:s');
		$notification->saveOrThrow();
	}
}