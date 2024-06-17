<?php

declare(strict_types=1);

namespace app\usecases\ChatMember;

use app\dto\ChatMember\CreateChatMemberLastEventDto;
use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\ChatMemberLastEvent;
use Throwable;

class ChatMemberLastEventService
{
	/**
	 * @throws SaveModelException
	 * @throws ModelNotFoundException
	 */
	public function updateOrCreate(CreateChatMemberLastEventDto $dto): ChatMemberLastEvent
	{
		$query = ChatMemberLastEvent::find()
			->andWhere([
				'chat_member_id' => $dto->chat_member_id,
				'event_chat_member_id' => $dto->event_chat_member_id,
			]);

		if ($query->exists()) {
			return $this->update($query->oneOrThrow());
		}

		return $this->create($dto);
	}

	/**
	 * @throws SaveModelException
	 */
	private function create(CreateChatMemberLastEventDto $dto): ChatMemberLastEvent
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
	private function update(ChatMemberLastEvent $model): ChatMemberLastEvent
	{
		$model->saveOrThrow();

		return $model;
	}
}