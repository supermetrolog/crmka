<?php

declare(strict_types=1);

namespace app\usecases\ChatMember;

use app\dto\ChatMember\CreateChatMemberLastEventDto;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\ChatMemberLastEvent;
use Throwable;

class ChatMemberLastEventService
{
	/**
	 * @throws SaveModelException
	 */
	public function create(CreateChatMemberLastEventDto $dto): ChatMemberLastEvent
	{
		$model = new ChatMemberLastEvent([
			'chat_member_id'       => $dto->chat_member_id,
			'event_chat_member_id' => $dto->event_chat_member_id,
		]);

		$model->saveOrThrow();

		return $model;
	}

	/**
	 * @throws SaveModelException
	 */
	public function update(ChatMemberLastEvent $model): ChatMemberLastEvent
	{
		$model->saveOrThrow();

		return $model;
	}
}