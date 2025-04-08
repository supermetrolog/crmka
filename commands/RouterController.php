<?php

declare(strict_types=1);

namespace app\commands;

use app\components\router\RouteCacheDumper;
use app\components\router\Router;
use app\kernel\common\controller\ConsoleController;
use Closure;
use Yii;

class RouterController extends ConsoleController
{
	private Router           $router;
	private RouteCacheDumper $dumper;

	public string   $cacheFilePath;
	private Closure $routerConfigDefinition;

	public function __construct($id, $module, Router $router, RouteCacheDumper $dumper, $config = [])
	{
		$this->router = $router;
		$this->dumper = $dumper;

		$this->cacheFilePath          = Yii::$app->params['router']['cacheFilePath'];
		$this->routerConfigDefinition = require Yii::$app->params['router']['routerConfigPath'];

		parent::__construct($id, $module, $config);
	}

	public function actionGenerate(): void
	{
		($this->routerConfigDefinition)($this->router);

		$this->dumper->saveToFile($this->cacheFilePath, $this->router->build());

		$this->success('✅  Routes generated successfully.');
	}
}