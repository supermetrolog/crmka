<?php

declare(strict_types=1);

namespace app\commands;

use app\actions\Import\ToOld\Companies;
use yii\console\Controller;

class ImportController extends Controller
{
	public function actions(): array
	{
		return [
			'companies' => Companies::class
		];
	}
}