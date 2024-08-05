<?php

declare(strict_types=1);

namespace app\usecases\ChatMember;

use app\dto\Call\CreateCallDto;
use app\dto\ChatMember\CreateChatMemberDto;
use app\dto\Relation\CreateRelationDto;
use app\helpers\DateTimeHelper;
use app\kernel\common\database\interfaces\transaction\TransactionBeginnerInterface;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\Call;
use app\models\ChatMember;
use app\models\ChatMemberMessage;
use app\usecases\Call\CreateCallService;
use app\usecases\Relation\RelationService;
use yii\db\Exception;
use \Throwable;

class ChatMemberService
{
	private TransactionBeginnerInterface $transactionBeginner;
	protected RelationService            $relationService;
	protected CreateCallService          $createCallService;

	public function __construct(
		TransactionBeginnerInterface $transactionBeginner,
		RelationService $relationService,
		CreateCallService $createCallService
	)
	{
		$this->transactionBeginner = $transactionBeginner;
		$this->relationService     = $relationService;
		$this->createCallService   = $createCallService;
	}

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

	/**
	 * @throws SaveModelException
	 * @throws Exception
	 * @throws Throwable
	 */
	public function createCall(ChatMember $member, CreateCallDto $dto): Call
	{
		$tx = $this->transactionBeginner->begin();

		try {
			$model = $this->createCallService->create($dto);

			$this->relationService->create(new CreateRelationDto([
				'first_type'  => $member::getMorphClass(),
				'first_id'    => $member->id,
				'second_type' => $model::getMorphClass(),
				'second_id'   => $model->id,
			]));

			$tx->commit();

			return $model;
		} catch (Throwable $th) {
			$tx->rollBack();
			throw $th;
		}
	}
}