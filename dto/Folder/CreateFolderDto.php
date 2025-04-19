<?php

declare(strict_types=1);

namespace app\dto\Folder;

use app\models\User;
use yii\base\BaseObject;

class CreateFolderDto extends BaseObject
{
	public User    $user;
	public string  $name;
	public ?string $color;
	public ?string $icon;
	public string  $morph;
} 