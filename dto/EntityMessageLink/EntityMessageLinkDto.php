<?php

declare(strict_types=1);

namespace app\dto\EntityMessageLink;

use app\models\ChatMemberMessage;
use app\models\User\User;
use yii\base\BaseObject;

class EntityMessageLinkDto extends BaseObject
{
	public int    $entity_id;
	public string $entity_type;

	public User              $user;
	public ChatMemberMessage $message;

	public string $kind;
} 