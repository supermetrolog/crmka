<?php

declare(strict_types=1);

namespace app\dto\Reminder;

use app\models\User;
use DateTimeInterface;
use yii\base\BaseObject;

class UpdateReminderDto extends BaseObject
{
	public User               $user;
	public string             $message;
	public int                $status;
	public ?DateTimeInterface $notify_at = null;
}