<?php

declare(strict_types=1);

namespace app\dto\Company;

use app\models\ChatMemberMessage;
use app\models\Company;
use app\models\User;
use yii\base\BaseObject;

class CompanyPinnedMessageDto extends BaseObject
{
	public Company           $company;
	public User              $user;
	public ChatMemberMessage $message;
} 