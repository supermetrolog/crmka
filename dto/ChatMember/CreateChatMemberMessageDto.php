<?php

declare(strict_types=1);

namespace app\dto\ChatMember;

use app\models\ChatMember;
use yii\base\BaseObject;

class CreateChatMemberMessageDto extends BaseObject
{
	public ChatMember $from;
	public ChatMember $to;
	public string     $message;
}