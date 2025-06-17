<?php

declare(strict_types=1);

namespace app\dto\Task;

use app\models\User;
use yii\base\BaseObject;

class LinkTaskRelationEntityDto extends BaseObject
{
	public int     $entityId;
	public string  $entityType;
	public string  $relationType;
	public ?string $comment = null;

	public ?User $createdBy = null;
}