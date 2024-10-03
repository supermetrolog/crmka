<?php

declare(strict_types=1);

namespace app\dto\Auth;

use yii\base\BaseObject;

class AuthUserAgentDto extends BaseObject
{
	public string $ip;
	public string $agent;
}