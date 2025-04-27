<?php

declare(strict_types=1);

namespace app\dto\Folder;

use yii\base\BaseObject;

class UpdateFolderDto extends BaseObject
{
	public string  $name;
	public ?string $color;
	public ?string $icon;
}