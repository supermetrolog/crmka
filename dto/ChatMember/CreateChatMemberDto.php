<?php

declare(strict_types=1);

namespace app\dto\ChatMember;

use yii\base\BaseObject;

class CreateChatMemberDto extends BaseObject
{
	public int    $model_id;
	public string $model_type;
}