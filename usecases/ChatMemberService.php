<?php

declare(strict_types=1);

namespace app\usecases;

use app\dto\ChatMember\CreateChatMemberDto;
use app\exceptions\domain\model\SaveModelException;
use app\helpers\DateTimeHelper;
use app\models\ChatMember;
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
			ChatMember::tableName(),
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
}