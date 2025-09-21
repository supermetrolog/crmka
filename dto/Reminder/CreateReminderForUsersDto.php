<?php

declare(strict_types=1);

namespace app\dto\Reminder;

use app\models\User\User;
use DateTimeInterface;
use yii\base\BaseObject;

class CreateReminderForUsersDto extends BaseObject
{
	public string             $message;
	public int                $status;
	public string             $created_by_type;
	public int                $created_by_id;
	public ?DateTimeInterface $notify_at = null;

	/**
	 * @var User[]
	 */
	public array $users;
}