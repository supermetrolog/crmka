<?php

declare(strict_types=1);

namespace app\dto\Auth;

use app\models\User;
use yii\base\BaseObject;

class AuthResponseDto extends BaseObject
{
	public User   $user;
	public string $accessToken;
}