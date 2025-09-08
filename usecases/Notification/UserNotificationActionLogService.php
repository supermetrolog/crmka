<?php

declare(strict_types=1);

namespace app\usecases\Notification;

use app\dto\UserNotification\UserNotificationActionLogDto;
use app\helpers\DateTimeHelper;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\Notification\UserNotificationActionLog;

class UserNotificationActionLogService
{
	/**
	 * @throws SaveModelException
	 */
	public function create(UserNotificationActionLogDto $dto): UserNotificationActionLog
	{
		$log = new UserNotificationActionLog();

		$log->user_notification_id = $dto->notificationId;
		$log->action_id            = $dto->actionId;
		$log->user_id              = $dto->userId;
		$log->executed_at          = DateTimeHelper::tryFormat($dto->executedAt);

		$log->saveOrThrow();

		return $log;
	}
}