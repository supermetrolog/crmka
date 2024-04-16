<?php

declare(strict_types=1);

namespace app\actions\ChatMember;

use app\dto\ChatMember\CreateChatMemberDto;
use app\kernel\common\actions\Action;
use app\models\ChatMember;
use app\models\Request;
use app\usecases\ChatMember\ChatMemberService;
use yii\base\ErrorException;
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
	 * @throws ErrorException
	 */
	public function run(): void
	{
		$query = Request::find()
		                ->joinWith(['chatMember'])
		                ->andWhereNull(ChatMember::field('id'));

		/** @var Request $request */
		foreach ($query->each(1000) as $request) {
			$this->service->upsert(new CreateChatMemberDto([
				'model_id'   => $request->id,
				'model_type' => Request::getMorphClass()
			]));

			$this->infof('Created request with ID: %d', $request->id);
		}
	}
}