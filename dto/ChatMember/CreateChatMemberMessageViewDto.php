<?php

declare(strict_types=1);

namespace app\dto\ChatMember;

use yii\base\BaseObject;

class CreateChatMemberMessageViewDto extends BaseObject
{
	public int $chat_member_id;
	public int $chat_member_message_id;
}