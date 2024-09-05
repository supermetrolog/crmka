<?php

declare(strict_types=1);

namespace app\dto\Task;

use app\models\User;
use DateTimeInterface;
use yii\base\BaseObject;

class UpdateTaskDto extends BaseObject
{
	public User               $user;
	public string             $message;
	public int                $status;
	public ?int               $created_by_id;
	public ?DateTimeInterface $start = null;
	public ?DateTimeInterface $end   = null;
	public array              $tagIds;
	public array              $observerIds;
}