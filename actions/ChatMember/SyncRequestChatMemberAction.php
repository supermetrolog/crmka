<?php

declare(strict_types=1);

namespace app\actions\ChatMember;

use app\dto\ChatMember\CreateChatMemberDto;
use app\kernel\common\actions\Action;
use app\models\Request;
use app\usecases\ChatMemberService;
use yii\db\Exception;

class SyncRequestChatMemberAction extends Action
{
	private ChatMemberService $service;

	public function __construct($id, $controller, ChatMemberService $service, array $config = [])
	{
		$this->service = $service;
		parent::__construct($id, $controller, $config);
	}

	/**
	 * @throws Exception
	 */
	public function run(): void
	{
		$query = Request::find();

		/** @var Request $request */
		foreach ($query->each(1000) as $request) {
			$this->service->upsert(new CreateChatMemberDto([
				'model_id'   => $request->id,
				'model_type' => Request::tableName()
			]));

			$this->info(sprintf('Created request with ID: %d', $request->id));
		}
	}
}