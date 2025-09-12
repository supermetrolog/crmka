<?php

declare(strict_types=1);

namespace app\dto\UserNotification;

use yii\base\BaseObject;

class CreateUserNotificationTemplateDto extends BaseObject
{
	public string $kind;
	public string $priority;
	public string $category;
	public bool   $isActive;
}