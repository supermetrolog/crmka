<?php

declare(strict_types=1);

namespace app\commands;

use app\actions\Company\ReassignCompanyConsultantsAction;
use app\kernel\common\controller\ConsoleController;

class NormalizeDataController extends ConsoleController
{
	public function actions(): array
	{
		return [
			'company-consultants' => ReassignCompanyConsultantsAction::class,
		];
	}
}