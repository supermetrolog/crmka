<?php

declare(strict_types=1);

namespace app\dto\Folder;

use app\models\Folder;
use yii\base\BaseObject;

class FolderEntityDto extends BaseObject
{
	public Folder $folder;
	public string $entity_type;
	public string $entity_id;
}