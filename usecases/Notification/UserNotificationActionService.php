<?php

declare(strict_types=1);

namespace app\usecases\Notification;

use app\dto\UserNotification\ProcessUserNotificationActionDto;
use app\dto\UserNotification\UserNotificationActionDto;
use app\dto\UserNotification\UserNotificationActionLogDto;
use app\helpers\DateTimeHelper;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\Notification\UserNotificationAction;

class UserNotificationActionService
{
	protected UserNotificationActionLogService $logService;

	public function __construct(UserNotificationActionLogService $logService)
	{
		$this->logService = $logService;
	}

	/**
	 * @throws SaveModelException
	 */
	public function create(UserNotificationActionDto $dto): UserNotificationAction
	{
		$action = new UserNotificationAction();

		$action->user_notification_id = $dto->notificationId;
		$action->type                 = $dto->type;
		$action->code                 = $dto->code;
		$action->label                = $dto->label;
		$action->icon                 = $dto->icon;
		$action->style                = $dto->style;
		$action->confirmation         = $dto->confirmation;
		$action->order                = $dto->order;
		$action->expires_at           = DateTimeHelper::tryFormat($dto->expiresAt);

		$action->setPayloadArray($dto->payload);

		$action->saveOrThrow();

		return $action;
	}

	/**
	 * @throws SaveModelException
	 */
	public function process(UserNotificationAction $action, ProcessUserNotificationActionDto $dto): void
	{
		$this->logService->create(
			new UserNotificationActionLogDto([
				'notificationId' => $action->user_notification_id,
				'actionId'       => $action->id,
				'userId'         => $dto->userId,
				'executedAt'     => $dto->executedAt
			])
		);
	}
}