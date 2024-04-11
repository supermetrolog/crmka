<?php

declare(strict_types=1);

namespace app\commands;

use app\actions\ChatMember\SyncRequestChatMemberAction;
use yii\console\Controller;

class SyncChatMemberController extends Controller
{

	public function actions(): array
	{
		return [
			'requests' => SyncRequestChatMemberAction::class
		];
	}
}