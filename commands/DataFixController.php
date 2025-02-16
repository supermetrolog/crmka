<?php

declare(strict_types=1);

namespace app\commands;

use app\actions\Company\TransferCompanyActivityAction;
use yii\console\Controller;

class DataFixController extends Controller
{

	public function actions(): array
	{
		return [
			'company-activity' => TransferCompanyActivityAction::class,
		];
	}
}