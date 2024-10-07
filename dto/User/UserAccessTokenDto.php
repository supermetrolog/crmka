<?php

declare(strict_types=1);

namespace app\dto\User;

use yii\base\BaseObject;

class UserAccessTokenDto extends BaseObject
{
	public int    $user_id;
	public string $access_token;
	public string $expires_at;
	public string $ip;
	public string $user_agent;
}