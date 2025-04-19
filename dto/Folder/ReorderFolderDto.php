<?php

declare(strict_types=1);

namespace app\dto\Folder;

use app\models\Folder;
use yii\base\BaseObject;

class ReorderFolderDto extends BaseObject
{
	public Folder $folder;
	public int    $sortOrder;
}