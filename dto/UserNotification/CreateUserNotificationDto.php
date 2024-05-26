<?php

declare(strict_types=1);

namespace app\dto\UserNotification;

use DateTimeInterface;
use yii\base\BaseObject;

class CreateUserNotificationDto extends BaseObject
{
	public int                $mailing_id;
	public int                $user_id;
	public ?DateTimeInterface $notified_at = null;
}