<?php

declare(strict_types=1);

namespace app\dto\Task;

use DateTimeInterface;
use yii\base\BaseObject;

class PostponeTaskDto extends BaseObject
{
	public DateTimeInterface $start;
	public DateTimeInterface $end;
}