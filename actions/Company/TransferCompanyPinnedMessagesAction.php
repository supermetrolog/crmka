<?php

declare(strict_types=1);

namespace app\actions\Company;

use app\dto\Company\CompanyPinnedMessageDto;
use app\kernel\common\actions\Action;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\ActiveQuery\ChatMemberQuery;
use app\models\ChatMemberMessage;
use app\models\Company;
use app\models\CompanyPinnedMessage;
use app\usecases\Company\CompanyPinnedMessageService;
use yii\base\ErrorException;

class TransferCompanyPinnedMessagesAction extends Action
{
	private CompanyPinnedMessageService $service;

	public function __construct(
		$id,
		$controller,
		CompanyPinnedMessageService $service,
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
		$this->info('Start transfer company comments from ChatMemberPinned to CompanyPinnedMessage');

		$query = ChatMemberMessage::find()
		                          ->alias('cmm')
		                          ->innerJoinWith(['toChatMember tcm' => function (ChatMemberQuery $query) {
			                          $query->andWhere('tcm.model_type = :type', [':type' => Company::getMorphClass()])
			                                ->andWhere('tcm.pinned_chat_member_message_id = cmm.id');
		                          }], false)
		                          ->leftJoin(['cpm' => CompanyPinnedMessage::getTable()], 'cpm.chat_member_message_id = cmm.id')
		                          ->with(['toChatMember', 'fromChatMember.user', 'toChatMember.company'])
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
			$pinned = $this->service->create(new CompanyPinnedMessageDto([
					'company' => $message->toChatMember->company,
					'message' => $message,
					'user'    => $message->fromChatMember->user
				])
			);

			$this->commentf('Transfer pinned message #%d to company #%d', $message->id, $pinned->company_id);
		}

		$this->infof('Complete. Transferred %d pinned messages', $query->count());
	}
}