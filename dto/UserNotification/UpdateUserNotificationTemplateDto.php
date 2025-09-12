<?php

declare(strict_types=1);

namespace app\dto\UserNotification;

use yii\base\BaseObject;

class UpdateUserNotificationTemplateDto extends BaseObject
{
	public string $priority;
	public string $category;
	public bool   $isActive;
}