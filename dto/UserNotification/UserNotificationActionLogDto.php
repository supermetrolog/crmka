<?php

declare(strict_types=1);

namespace app\dto\UserNotification;

use DateTimeInterface;
use yii\base\BaseObject;

class UserNotificationActionLogDto extends BaseObject
{
	public int                $notificationId;
	public int                $userId;
	public int                $actionId;
	public ?DateTimeInterface $executedAt = null;
}