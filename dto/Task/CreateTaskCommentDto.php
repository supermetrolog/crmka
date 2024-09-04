<?php

declare(strict_types=1);

namespace app\dto\Task;

use yii\base\BaseObject;

class CreateTaskCommentDto extends BaseObject
{
	public string $message;
	public int    $created_by_id;
	public int    $task_id;
}