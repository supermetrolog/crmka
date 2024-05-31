<?php

declare(strict_types=1);

namespace app\dto\Notification;

use app\components\Notification\Interfaces\NotifiableInterface;
use yii\base\BaseObject;

class CreateNotificationDto extends BaseObject
{
	public string              $channel;
	public string              $subject;
	public string              $message;
	public NotifiableInterface $notifiable;
	public string              $created_by_type;
	public int                 $created_by_id;
}
