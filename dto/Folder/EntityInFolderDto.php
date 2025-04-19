<?php

declare(strict_types=1);

namespace app\dto\Folder;

use yii\base\BaseObject;

class EntityInFolderDto extends BaseObject
{
	public string $entity_type;
	public string $entity_id;
}