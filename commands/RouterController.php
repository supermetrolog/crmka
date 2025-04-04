<?php

declare(strict_types=1);

namespace app\commands;

use app\components\router\RouteCacheDumper;
use app\components\router\Router;
use app\kernel\common\controller\ConsoleController;
use Yii;

class RouterController extends ConsoleController
{
	private RouteCacheDumper $dumper;

	public function __construct($id, $module, RouteCacheDumper $dumper, $config = [])
	{
		$this->dumper = $dumper;
		parent::__construct($id, $module, $config);
	}

	public function actionGenerate(): void
	{
		$router = new Router();

		$definition = require Yii::getAlias('@app/config/common/web/routes.php');
		$definition($router);

		$path = Yii::getAlias('@app/config/common/web/url_rules.php');

		$this->dumper->saveToFile($path, $router->build());

		$this->success('✅  Routes generated successfully.');
	}
}