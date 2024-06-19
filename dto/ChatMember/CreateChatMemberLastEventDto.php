<?php

declare(strict_types=1);

namespace app\dto\ChatMember;

use yii\base\BaseObject;

class CreateChatMemberLastEventDto extends BaseObject
{
	public int $chat_member_id;
	public int $event_chat_member_id;
}