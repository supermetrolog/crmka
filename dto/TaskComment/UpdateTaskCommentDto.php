<?php

declare(strict_types=1);

namespace app\dto\TaskComment;

use yii\base\BaseObject;

class UpdateTaskCommentDto extends BaseObject
{
	public string $message;
}