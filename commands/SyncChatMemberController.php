<?php

declare(strict_types=1);

namespace app\commands;

use app\actions\ChatMember\SyncCommercialOfferChatMemberAction;
use app\actions\ChatMember\SyncObjectChatMemberAction;
use app\actions\ChatMember\SyncRequestChatMemberAction;
use app\actions\ChatMember\SyncUserChatMemberAction;
use yii\console\Controller;

class SyncChatMemberController extends Controller
{

	public function actions(): array
	{
		return [
			'requests' => SyncRequestChatMemberAction::class,
			//			'commercial-offers' => SyncCommercialOfferChatMemberAction::class,
			'users'    => SyncUserChatMemberAction::class,
			'objects'  => SyncObjectChatMemberAction::class,
		];
	}
}