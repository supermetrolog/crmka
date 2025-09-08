<?php

declare(strict_types=1);

namespace app\dto\UserNotification;

use yii\base\BaseObject;

class UserNotificationRelationDto extends BaseObject
{
	public ?int $notificationId = null;

	public string $entityType;
	public int    $entityId;
}