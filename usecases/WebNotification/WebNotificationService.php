<?php

declare(strict_types=1);

namespace app\usecases\WebNotification;

use app\dto\WebNotification\CreateWebNotificationDto;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\Notification\WebNotification;

class WebNotificationService
{
	/**
	 * @throws SaveModelException
	 */
	public function create(CreateWebNotificationDto $dto): WebNotification
	{
		$model = new WebNotification();

		$model->user_id              = $dto->user_id;
		$model->user_notification_id = $dto->user_notification_id;
		$model->subject              = $dto->subject;
		$model->message              = $dto->message;
		$model->viewed_at            = $dto->viewed_at ? $dto->viewed_at->format('Y-m-d H:i:s') : null;

		$model->saveOrThrow();

		return $model;
	}
}