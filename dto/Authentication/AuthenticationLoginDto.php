<?php

declare(strict_types=1);

namespace app\dto\Authentication;

use yii\base\BaseObject;

class AuthenticationLoginDto extends BaseObject
{
	public string $username;
	public string $password;
}