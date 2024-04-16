<?php

declare(strict_types=1);

namespace app\usecases\ChatMember;

use app\dto\ChatMember\CreateChatMemberMessageDto;
use app\dto\ChatMember\UpdateChatMemberMessageDto;
use app\exceptions\domain\model\SaveModelException;
use app\models\ChatMemberMessage;

class ChatMemberMessageService
{

	/**
	 * @throws SaveModelException
	 */
	public function create(CreateChatMemberMessageDto $dto): ChatMemberMessage
	{
		$message = new ChatMemberMessage();

		$message->from_chat_member_id = $dto->from->id;
		$message->to_chat_member_id   = $dto->to->id;

		$message->message = $dto->message;

		$message->saveOrThrow();

		return $message;
	}


	/**
	 * @throws SaveModelException
	 */
	public function update(ChatMemberMessage $message, UpdateChatMemberMessageDto $dto): ChatMemberMessage
	{
		$message->message = $dto->message;

		$message->saveOrThrow();

		return $message;
	}
}