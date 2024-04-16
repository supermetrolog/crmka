<?php

declare(strict_types=1);

namespace app\dto\ChatMember;

use app\models\ChatMember;
use yii\base\BaseObject;

class UpdateChatMemberMessageDto extends BaseObject
{
	public string     $message;
}