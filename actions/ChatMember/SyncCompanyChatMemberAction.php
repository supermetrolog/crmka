<?php

declare(strict_types=1);

namespace app\actions\ChatMember;

use app\dto\ChatMember\CreateChatMemberDto;
use app\kernel\common\actions\Action;
use app\models\ChatMember;
use app\models\Company;
use app\usecases\ChatMember\ChatMemberService;
use yii\base\ErrorException;
use yii\db\Exception;

class SyncCompanyChatMemberAction extends Action
{
	private ChatMemberService $service;

	public function __construct($id, $controller, ChatMemberService $service, array $config = [])
	{
		$this->service = $service;
		parent::__construct($id, $controller, $config);
	}

	/**
	 * @throws Exception
	 * @throws ErrorException
	 */
	public function run(): void
	{
		$query = Company::find()
		                ->joinWith(['chatMember'])
		                ->andWhereNull(ChatMember::field('id'));

		/** @var Company $company */
		foreach ($query->each(1000) as $company) {
			$this->service->upsert(new CreateChatMemberDto([
				'model_id'   => $company->id,
				'model_type' => Company::getMorphClass()
			]));

			$this->infof('Created company with ID: %d', $company->id);
		}
	}
}