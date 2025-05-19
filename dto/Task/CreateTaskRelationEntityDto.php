<?php

declare(strict_types=1);

namespace app\dto\Task;

use app\models\Task;
use app\models\User;
use yii\base\BaseObject;

class CreateTaskRelationEntityDto extends BaseObject
{
	public int     $entityId;
	public string  $entityType;
	public string  $relationType;
	public ?string $comment;

	public ?User $createdBy;
	public Task  $task;
}