<?php

declare(strict_types=1);

namespace app\dto\User;

use yii\base\BaseObject;

class ChangeUserPasswordDto extends BaseObject
{
	public string $currentPassword;
	public string $newPassword;
}