<?php

declare(strict_types=1);

namespace app\dto\Authentication;

use app\models\User;
use yii\base\BaseObject;

class AuthenticationResponseDto extends BaseObject
{
	public User   $user;
	public string $accessToken;
}