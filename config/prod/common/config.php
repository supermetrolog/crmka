<?php

$secrets               = require YII_PROJECT_ROOT . "/config/secrets.php";
$prod_common_this_host = "https://api.pennylane.pro/";

return [
	'params'     => [
		'synchronizer' => [
			'this_project'     => [
				'source_repo_dir_path' => "/home/user/web/api.pennylane.pro/public_html",
			],
			'objects_project'  => [
				'source_repo_dir_path' => "/home/user/web/pennylane.pro/public_html",
			],
			'frontend_project' => [
				'source_repo_dir_path' => "/home/user/web/clients.pennylane.pro/public_html",
			]
		],
		'pythonPath'   => '/bin/python3',
		'url'          => [
			'this_host'       => $prod_common_this_host,
			'image_not_found' => "{$prod_common_this_host}images/no-image.jpg",
			'empty_image'     => "{$prod_common_this_host}images/empty.jpg"
		],
	],
	'container'  => [
		'singletons' => [
			'db'     => [
				'enableSchemaCache'   => true,
				'schemaCacheDuration' => 120,
				'schemaCache'         => 'cache',
			],
			'old_db' => [
				'enableSchemaCache'   => true,
				'schemaCacheDuration' => 120,
				'schemaCache'         => 'cache',
			],
		],
	],
	'components' => [
		'log' => [
			'targets' => [
				[
					'class'    => 'airani\log\TelegramTarget',
					'levels'   => ['error', 'warning'],
					'logVars'  => [],
					'botToken' => $secrets['tg_logger_bot']['token'], // bot token secret key
					'chatId'   => $secrets['tg_logger_bot']['channel'], // chat id or channel username with @ like 12345 or @channel
					'except'   => [
						'yii\web\HttpException:401',
						'yii\web\HttpException:404'
					]
				],
				[
					'class'         => 'notamedia\sentry\SentryTarget',
					'dsn'           => $secrets['sentry']['dsn'],
					'levels'        => ['error', 'warning'],
					'context'       => true,
					'clientOptions' => ['release' => 'stg']
				],
			]
		],
	]
];
