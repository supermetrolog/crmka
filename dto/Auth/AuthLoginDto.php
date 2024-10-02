<?php

declare(strict_types=1);

namespace app\dto\Auth;

use yii\base\BaseObject;

class AuthLoginDto extends BaseObject
{
	public string $username;
	public string $password;
}