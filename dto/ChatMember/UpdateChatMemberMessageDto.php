<?php

declare(strict_types=1);

namespace app\dto\ChatMember;

use yii\base\BaseObject;

class UpdateChatMemberMessageDto extends BaseObject
{
	public string $message;
	public array  $contactIds;
	public array  $tagIds;
	public array  $currentFiles;
}