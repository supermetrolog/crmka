<?php

declare(strict_types=1);

namespace app\commands;

use app\helpers\ArrayHelper;
use app\helpers\BenchmarkHelper;
use Exception;
use yii\console\Controller;

class TestController extends Controller
{

	public function actionIndex(): void
	{
		throw new Exception('test exeption');
	}
}