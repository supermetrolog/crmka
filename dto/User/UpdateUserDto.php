<?php

declare(strict_types=1);

namespace app\dto\User;

use yii\base\BaseObject;

class UpdateUserDto extends BaseObject
{
	public ?string $email;
	public ?string $email_username;
	public ?string $email_password;
	public string  $role;
	public ?string $password;
}