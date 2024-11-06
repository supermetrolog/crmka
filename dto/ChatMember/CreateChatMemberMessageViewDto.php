<?php

declare(strict_types=1);

namespace app\dto\ChatMember;

use app\models\ChatMember;
use app\models\ChatMemberMessage;
use yii\base\BaseObject;

class CreateChatMemberMessageViewDto extends BaseObject
{
	public ChatMemberMessage $message;
	public ChatMember        $fromChatMember;
}