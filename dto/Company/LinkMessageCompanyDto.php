<?php

declare(strict_types=1);

namespace app\dto\Company;

use app\models\ChatMemberMessage;
use app\models\User\User;
use yii\base\BaseObject;

class LinkMessageCompanyDto extends BaseObject
{
	public User              $user;
	public ChatMemberMessage $message;

	public string $kind;
} 