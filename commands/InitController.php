<?php

declare(strict_types=1);

namespace app\commands;

use app\actions\TaskHistory\InitTaskHistoryAction;
use yii\console\Controller;

class InitController extends Controller
{
	public function actions(): array
	{
		return [
			'task-history' => InitTaskHistoryAction::class,
		];
	}
}