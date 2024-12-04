<?php

declare(strict_types=1);

namespace app\dto\User;

use yii\base\BaseObject;

class UserActivityDto extends BaseObject
{
	public int     $user_id;
	public string  $ip;
	public string  $user_agent;
	public ?string $last_page;
}