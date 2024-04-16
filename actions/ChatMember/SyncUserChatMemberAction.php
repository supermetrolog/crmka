<?php

declare(strict_types=1);

namespace app\actions\ChatMember;

use app\dto\ChatMember\CreateChatMemberDto;
use app\kernel\common\actions\Action;
use app\models\ChatMember;
use app\models\Request;
use app\models\User;
use app\usecases\ChatMemberService;
use yii\base\ErrorException;
use yii\db\Exception;

class SyncUserChatMemberAction extends Action
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
		$query = User::find()
		             ->joinWith(['chatMember'])
		             ->andWhereNull(ChatMember::field('id'));

		/** @var User $user */
		foreach ($query->each(1000) as $user) {
			$this->service->upsert(new CreateChatMemberDto([
				'model_id'   => $user->id,
				'model_type' => User::getMorphClass()
			]));

			$this->infof('Created user with ID: %d', $user->id);
		}
	}
}