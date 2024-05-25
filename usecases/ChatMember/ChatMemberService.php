<?php

declare(strict_types=1);

namespace app\usecases\ChatMember;

use app\dto\ChatMember\CreateChatMemberDto;
use app\helpers\DateTimeHelper;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\ChatMember;
use app\models\ChatMemberMessage;
use yii\db\Exception;

class ChatMemberService
{

	/**
	 * @throws SaveModelException
	 */
	public function create(CreateChatMemberDto $dto): ChatMember
	{
		$chatMember = new ChatMember();

		$chatMember->model_id   = $dto->model_id;
		$chatMember->model_type = $dto->model_type;

		$chatMember->saveOrThrow();

		return $chatMember;
	}

	/**
	 * TODO: make multiple upsert
	 *
	 * @param CreateChatMemberDto $dto
	 *
	 * @return void
	 * @throws Exception
	 */
	public function upsert(CreateChatMemberDto $dto): void
	{
		$now = DateTimeHelper::nowf();

		ChatMember::upsert(
			[
				'model_id'   => $dto->model_id,
				'model_type' => $dto->model_type,
				'created_at' => $now,
				'updated_at' => $now,
			],
			[
				'updated_at' => $now,
			]
		);
	}

	/**
	 * @throws SaveModelException
	 */
	public function pinMessage(ChatMember $member, ChatMemberMessage $message)
	{
		$member->pinned_chat_member_message_id = $message->id;
		$member->saveOrThrow();
	}

	/**
	 * @throws SaveModelException
	 */
	public function unpinMessage(ChatMember $member)
	{
		$member->pinned_chat_member_message_id = null;
		$member->saveOrThrow();
	}
}