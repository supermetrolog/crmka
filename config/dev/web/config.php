<?php

use yii\helpers\ArrayHelper;

return ArrayHelper::merge(
	require __DIR__ . "/../common/config.php",
	[
		'bootstrap' => ['gii', 'debug'],
		'modules'   => [
			'debug' => [
				'class'      => 'yii\debug\Module',
				'allowedIPs' => ["*"],
			],
			'gii'   => [
				'class'      => 'yii\gii\Module',
				'allowedIPs' => ["*"],
				'generators' => [
					'jobs'       => yii\queue\gii\Generator::class,
					'model'      => [
						'class'     => \yii\gii\generators\model\Generator::class,
						'templates' => [
							'custom' => '@app/kernel/common/gii/views/model'
						]
					],
					'crud' => [
						'class'     => \yii\gii\generators\crud\Generator::class,
						'templates' => [
							'custom' => '@app/kernel/common/gii/views/crud'
						]
					]
				],
			]
		]
	]
);
