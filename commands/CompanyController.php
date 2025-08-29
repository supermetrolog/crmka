<?php

declare(strict_types=1);

namespace app\commands;

use app\actions\Company\EnforceCompanyStatusesAction;
use app\kernel\common\controller\ConsoleController;

class CompanyController extends ConsoleController
{
	public function actions(): array
	{
		return [
			'enforce-statuses' => EnforceCompanyStatusesAction::class,
		];
	}
}