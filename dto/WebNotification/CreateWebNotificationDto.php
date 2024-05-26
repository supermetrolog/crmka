<?php

declare(strict_types=1);

namespace app\dto\WebNotification;

use DateTime;
use yii\base\BaseObject;

class CreateWebNotificationDto extends BaseObject
{
	public int       $user_id;
	public int       $user_notification_id;
	public string    $subject;
	public string    $message;
	public ?DateTime $viewed_at = null;
}