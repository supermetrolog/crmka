<?php

declare(strict_types=1);

namespace app\actions\ChatMember;

use app\dto\ChatMember\CreateChatMemberDto;
use app\kernel\common\actions\Action;
use app\models\ChatMember;
use app\models\CommercialOffer;
use app\usecases\ChatMember\ChatMemberService;
use yii\base\ErrorException;
use yii\db\Exception;

class SyncCommercialOfferChatMemberAction extends Action
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
		$query = CommercialOffer::find()
		                        ->joinWith(['chatMember'])
		                        ->andWhereNull(ChatMember::field('id'));

		/** @var CommercialOffer $request */
		foreach ($query->each(1000) as $request) {
			$this->service->upsert(new CreateChatMemberDto([
				'model_id'   => $request->id,
				'model_type' => CommercialOffer::getMorphClass()
			]));

			$this->info(sprintf('Created commercial offer with ID: %d', $request->id));
		}
	}
}