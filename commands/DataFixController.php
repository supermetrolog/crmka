<?php

declare(strict_types=1);

namespace app\commands;

use app\actions\Call\SetContactCallMainPhone;
use app\actions\ChatMemberMessage\FixSurveyChatMemberMessagesAction;
use app\actions\Company\FixCompanyCategoriesAction;
use app\actions\Company\FixCompanyProductRangesAction;
use app\actions\Company\TransferCompanyActivityAction;
use app\actions\Company\TransferCompanyPinnedMessagesAction;
use app\actions\Contact\FixContactPositionsAction;
use app\actions\Task\FixTaskScheduledCallsActions;
use app\actions\Task\TaskMessageToTitleAction;
use yii\console\Controller;

class DataFixController extends Controller
{

	public function actions(): array
	{
		return [
			'company-activity'            => TransferCompanyActivityAction::class,
			'company-product-ranges'      => FixCompanyProductRangesAction::class,
			'task-message-to-title'       => TaskMessageToTitleAction::class,
			'company-pinned-messages'     => TransferCompanyPinnedMessagesAction::class,
			'company-categories'          => FixCompanyCategoriesAction::class,
			'task-scheduled-calls'        => FixTaskScheduledCallsActions::class,
			'survey-chat-member-messages' => FixSurveyChatMemberMessagesAction::class,
			'contact-call-main-phone'     => SetContactCallMainPhone::class,
			'contact-positions'           => FixContactPositionsAction::class
		];
	}
}