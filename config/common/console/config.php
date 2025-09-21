<?php

use app\commands\TelegramController;
use app\components\Media\Media;
use app\components\PathBuilder\PathBuilderFactory;
use yii\di\Container;
use yii\helpers\ArrayHelper;

$params = require YII_PROJECT_ROOT . '/config/common/common/params.php';

return ArrayHelper::merge(
	require __DIR__ . '/../common/config.php',
	[
		'id'                  => 'basic-console',
		'bootstrap'           => ['queue'],
		'aliases'             => [
			'@tests' => '@app/tests'
		],
		'controllerNamespace' => 'app\commands',
		'controllerMap'       => [
			'migrate'  => [
				'class'                  => 'yii\console\controllers\MigrateController',
				'templateFile'           => '@app/kernel/console/views/migration.php',
				'generatorTemplateFiles' => [
					'create_table'    => '@app/kernel/console/views/createTableMigration.php',
					'drop_table'      => '@yii/views/dropTableMigration.php',
					'add_column'      => '@yii/views/addColumnMigration.php',
					'drop_column'     => '@yii/views/dropColumnMigration.php',
					'create_junction' => '@yii/views/createTableMigration.php',
				]
			],
			'telegram' => [
				'class'         => TelegramController::class,
				'webhookSecret' => $params['crm_telegram_bot']['webhook']['secret'],
				'webhookUrl'    => $params['crm_telegram_bot']['webhook']['url'],
			]
		],
		'container'           => [
			'singletons' => [
				Media::class => function (Container $container) {
					return new Media(
						$container->get(PathBuilderFactory::class),
						Yii::$app->params['media']['baseUrl'],
						Yii::$app->params['media']['diskPath']
					);
				}
			]
		],
	]
);
