<?php

declare(strict_types=1);

namespace app\commands;

use app\actions\Company\FixCompanyProductRangesAction;
use app\actions\Company\TransferCompanyActivityAction;
use app\actions\Task\TaskMessageToTitleAction;
use yii\console\Controller;

class DataFixController extends Controller
{

	public function actions(): array
	{
		return [
			'company-activity'       => TransferCompanyActivityAction::class,
			'company-product-ranges' => FixCompanyProductRangesAction::class,
			'task-message-to-title'  => TaskMessageToTitleAction::class
		];
	}
}