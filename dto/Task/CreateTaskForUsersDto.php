<?php

declare(strict_types=1);

namespace app\dto\Task;

use app\models\User;
use DateTimeInterface;
use yii\base\BaseObject;

class CreateTaskForUsersDto extends BaseObject
{
	public string             $message;
	public int                $status;
	public ?DateTimeInterface $start = null;
	public ?DateTimeInterface $end   = null;
	public string             $created_by_type;
	public int                $created_by_id;

	/**
	 * @var User[]
	 */
	public array $users;
}