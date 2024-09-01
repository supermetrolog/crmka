<?php

declare(strict_types=1);

namespace app\dto\TaskObserver;

use yii\base\BaseObject;

class CreateTaskObserverDto extends BaseObject
{
	public int $user_id;
	public int $task_id;
	public int $created_by_id;
}