<?php

namespace app\dto\TaskFavorite;

use yii\base\BaseObject;

class TaskFavoriteDto extends BaseObject
{
	public int $task_id;
	public int $user_id;
}