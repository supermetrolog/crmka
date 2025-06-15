<?php

declare(strict_types=1);

namespace app\dto\Company;

use app\models\ChatMemberMessage;
use app\models\User;
use yii\base\BaseObject;

class PinMessageCompanyDto extends BaseObject
{
	public User              $user;
	public ChatMemberMessage $message;
} 