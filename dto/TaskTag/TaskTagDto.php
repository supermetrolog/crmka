<?php

declare(strict_types=1);

namespace app\dto\TaskTag;

use yii\base\BaseObject;

class TaskTagDto extends BaseObject
{
	public string $name;
	public string $description;
	public string $color;
}