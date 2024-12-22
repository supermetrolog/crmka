<?php

namespace app\models\search\ChatMember;

use app\models\Company;
use app\models\ObjectChatMember;
use app\models\Request;
use app\models\search\ChatMember\Strategies\CompanyChatMemberSearchStrategy;
use app\models\search\ChatMember\Strategies\ObjectChatMemberSearchStrategy;
use app\models\search\ChatMember\Strategies\RequestChatMemberSearchStrategy;
use app\models\search\ChatMember\Strategies\UserChatMemberSearchStrategy;
use app\models\User;
use InvalidArgumentException;

class ChatMemberSearchStrategyFactory
{
	public function create(?string $type): ChatMemberSearchStrategyInterface
	{
		switch ($type) {
			case User::getMorphClass():
				return new UserChatMemberSearchStrategy();
			case Company::getMorphClass():
				return new CompanyChatMemberSearchStrategy();
			case ObjectChatMember::getMorphClass():
				return new ObjectChatMemberSearchStrategy();
			case Request::getMorphClass():
				return new RequestChatMemberSearchStrategy();
			default:
				throw new InvalidArgumentException("Unexpected ChatMember type: $type");
		}
	}
}