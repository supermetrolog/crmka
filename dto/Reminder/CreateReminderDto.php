<?php

declare(strict_types=1);

namespace app\dto\Reminder;

use app\models\User;
use DateTimeInterface;
use yii\base\BaseObject;

class CreateReminderDto extends BaseObject
{
	public User               $user;
	public string             $message;
	public int                $status;
	public string             $created_by_type;
	public int                $created_by_id;
	public ?DateTimeInterface $notify_at = null;
}