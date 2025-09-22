<?php

declare(strict_types=1);

namespace app\dto\Task;

use app\models\ChatMember;
use app\models\User\User;
use yii\base\BaseObject;

class TaskAssignDto extends BaseObject
{
	public User $user;

	/** @var User|ChatMember */
	public        $assignedBy;
	public string $comment;
}