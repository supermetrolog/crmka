<?php

declare(strict_types=1);

namespace app\dto\TaskComment;

use app\models\Task;
use app\models\User;
use yii\base\BaseObject;

class UpdateTaskCommentDto extends BaseObject
{
	public string $message;
	public User   $createdBy;
	public Task   $task;
}