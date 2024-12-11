<?php

declare(strict_types=1);

namespace app\dto\TaskHistory;

use app\models\Task;
use app\models\User;
use yii\base\BaseObject;

class TaskHistoryDto extends BaseObject
{
	public Task $task;
	public User $createdBy;
}