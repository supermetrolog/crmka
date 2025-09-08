<?php

declare(strict_types=1);

namespace app\dto\UserNotification;

use DateTimeInterface;
use yii\base\BaseObject;

class ProcessUserNotificationActionDto extends BaseObject
{
	public int                $userId;
	public ?DateTimeInterface $executedAt = null;
}