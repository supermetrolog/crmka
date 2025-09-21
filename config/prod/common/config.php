<?php

$secrets               = require YII_PROJECT_ROOT . "/config/secrets.php";
$prod_common_this_host = "https://api.raysen.ru/";

return [
	'params'     => [
		'synchronizer'     => [
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
		'pythonPath'       => '/bin/python3',
		'url'              => [
			'this_host'       => $prod_common_this_host,
			'image_not_found' => "{$prod_common_this_host}images/no-image.jpg",
			'empty_image'     => "{$prod_common_this_host}images/empty.jpg"
		],
		'crm_telegram_bot' => [
			'name'     => 'crm_raysen_bot',
			'apiUrl'   => "https://api.telegram.org/bot{$secrets['crm_telegram_bot']['token']}",
			'webhook'  => [
				'secretHeader' => 'X-Telegram-Bot-Api-Secret-Token',
				'secret'       => $secrets['crm_telegram_bot']['webhook']['secret'],
				'url'          => "{$prod_common_this_host}integration/telegram/webhook",
			],
			'deepLink' => [
				'webBase' => 'https://t.me',
				'appBase' => 'tg://resolve',
				'param'   => 'start',
				'prefer'  => 'web',
			]
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
