<?php

declare(strict_types=1);

namespace app\dto\Task;

use app\models\User;
use DateTimeInterface;
use yii\base\BaseObject;

class ChangeTaskStatusDto extends BaseObject
{
	public int                $status;
	public ?string            $comment       = null;
	public ?DateTimeInterface $impossible_to = null;
	public ?User              $changedBy     = null;
}