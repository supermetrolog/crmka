<?php

declare(strict_types=1);

namespace app\usecases\ChatMember;

use app\dto\ChatMember\CreateChatMemberMessageViewDto;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\ChatMemberMessageView;
use Throwable;

class ChatMemberMessageViewService
{
	/**
	 * @throws SaveModelException
	 */
	public function create(CreateChatMemberMessageViewDto $dto): ChatMemberMessageView
	{
		$model = new ChatMemberMessageView([
			'chat_member_id'         => $dto->message->to_chat_member_id,
			'chat_member_message_id' => $dto->message->id,
		]);

		$model->saveOrThrow();

		return $model;
	}

	/**
	 * @throws Throwable
	 */
	public function delete(ChatMemberMessageView $model): void
	{
		$model->delete();
	}
}