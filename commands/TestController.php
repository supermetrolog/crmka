<?php

declare(strict_types=1);

namespace app\commands;

use app\helpers\ArrayHelper;
use app\helpers\BenchmarkHelper;
use yii\console\Controller;

class TestController extends Controller
{

	public function actionIndex(): void
	{
		$array   = [20, 120, 220, 320, 420, 520, 620, 720, 820, 920];
		$clients = 2_000;

		BenchmarkHelper::testWithArgs(
			[ArrayHelper::class, 'toDistributedValue'],
			[$array, $clients],
			10,
			true,
			'toDistributedValue'
		);
	}
}