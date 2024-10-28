<?php

declare(strict_types=1);

namespace app\dto\ChatMemberView;

use yii\base\BaseObject;

class StatisticChatMemberViewDto extends BaseObject
{
	/** @var array<string> */
	public array $model_types;

	/** @var array<int> */
	public array $chat_member_ids;
}