<?php

use yii\helpers\ArrayHelper;

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
			'migrate' => [
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
		],
	]
);
