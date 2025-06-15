<?php

declare(strict_types=1);

namespace app\dto\EntityPinnedMessage;

use app\models\ChatMemberMessage;
use app\models\User;
use yii\base\BaseObject;

class EntityPinnedMessageDto extends BaseObject
{
	public int    $entity_id;
	public string $entity_type;

	public User              $user;
	public ChatMemberMessage $message;
} 