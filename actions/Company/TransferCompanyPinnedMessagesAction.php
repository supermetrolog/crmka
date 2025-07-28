<?php

declare(strict_types=1);

namespace app\actions\Company;

use app\dto\EntityMessageLink\EntityMessageLinkDto;
use app\enum\EntityMessageLink\EntityMessageLinkKindEnum;
use app\kernel\common\actions\Action;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\ActiveQuery\ChatMemberQuery;
use app\models\ChatMemberMessage;
use app\models\Company;
use app\models\EntityMessageLink;
use app\usecases\EntityMessageLink\EntityMessageLinkService;
use yii\base\ErrorException;

class TransferCompanyPinnedMessagesAction extends Action
{
	private EntityMessageLinkService $service;

	public function __construct(
		$id,
		$controller,
		EntityMessageLinkService $service,
		array $config = []
	)
	{
		$this->service = $service;

		parent::__construct($id, $controller, $config);
	}


	/**
	 * @throws SaveModelException
	 * @throws ErrorException
	 */
	public function run(): void
	{
		$this->info('Start transfer company comments from ChatMemberPinned to EntityPinnedMessage');

		$query = ChatMemberMessage::find()
		                          ->alias('cmm')
		                          ->innerJoinWith(['toChatMember tcm' => function (ChatMemberQuery $query) {
			                          $query->andWhere('tcm.model_type = :type', [':type' => Company::getMorphClass()])
			                                ->andWhere('tcm.pinned_chat_member_message_id = cmm.id');
		                          }], false)
		                          ->leftJoin(['cpm' => EntityMessageLink::getTable()], 'cpm.chat_member_message_id = cmm.id')
		                          ->with(['toChatMember', 'fromChatMember.user'])
		                          ->andWhere('cmm.deleted_at is null')
		                          ->andWhere('cpm.id is null');

		$count = (int)$query->count();

		if ($count === 0) {
			$this->info('No pinned messages found, skipping...');

			return;
		}

		$this->infof('Found %d pinned messages', $count);

		/** @var ChatMemberMessage $message */
		foreach ($query->each() as $message) {
			$pinned = $this->service->create(new EntityMessageLinkDto([
					'entity_id'   => $message->toChatMember->model_id,
					'entity_type' => $message->toChatMember->model_type,
					'message'     => $message,
					'user'        => $message->fromChatMember->user,
					'kind'        => EntityMessageLinkKindEnum::COMMENT
				])
			);

			$this->commentf('Transfer pinned message #%d to company #%d', $message->id, $pinned->entity_id);
		}

		$this->infof('Complete. Transferred %d pinned messages', $query->count());
	}
}