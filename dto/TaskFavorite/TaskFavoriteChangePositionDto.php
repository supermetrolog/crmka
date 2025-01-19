<?php

namespace app\dto\TaskFavorite;

use yii\base\BaseObject;

class TaskFavoriteChangePositionDto extends BaseObject
{
	public ?int $prev_id = null;
	public ?int $next_id = null;
} 