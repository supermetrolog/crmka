<?php

use yii\di\Container;

$common_params = require __DIR__ . "/params.php";
$common_db     = require __DIR__ . "/db.php";
$common_db_old = require __DIR__ . "/db_old.php";

return [
	'notify'      => [
		'class' => app\components\NotificationService::class,
	],
	'notifyQueue' => [
		'class'        => app\components\NotificationsQueueService::class,
		'host'         => 'localhost',
		'port'         => 5672,
		'user'         => $common_params['rabbit']['user'],
		'password'     => $common_params['rabbit']['password'],
		'queueName'    => $common_params['rabbit']['notify']['queueName'],
		'exchangeName' => $common_params['rabbit']['notify']['exchangeName'],
	],
	'queue'       => [
		'class'        => \yii\queue\amqp_interop\Queue::class,
		'port'         => 5672,
		'user'         => $common_params['rabbit']['user'],
		'password'     => $common_params['rabbit']['password'],
		'queueName'    => $common_params['rabbit']['queueName'],
		'exchangeName' => $common_params['rabbit']['exchangeName'],
		'driver'       => yii\queue\amqp_interop\Queue::ENQUEUE_AMQP_LIB,
	],
	'formatter'   => [
		'class'                  => \yii\i18n\Formatter::className(),
		'dateFormat'             => 'long',
		'currencyCode'           => 'RUB',
		'decimalSeparator'       => '.',
		'thousandSeparator'      => ' ',
		'nullDisplay'            => '',
		'numberFormatterOptions' => [
			NumberFormatter::MIN_FRACTION_DIGITS => 0,
			NumberFormatter::MAX_FRACTION_DIGITS => 2,
		]
	],
	'authManager' => [
		'class' => 'yii\rbac\DbManager'
	],
	'cache'       => [
		'class' => 'yii\caching\FileCache',
	],
	'log'         => [
		'targets' => [
			[
				'class'  => 'yii\log\FileTarget',
				'levels' => ['error', 'warning'],
			]
		]
	],
	'db'          => fn() => Yii::$container->get('db'),
	'db_old'      => fn() => Yii::$container->get('old_db'),
	'media' => [
		'class'  => \app\components\MediaService::class,
		'path' => dirname(__DIR__, 3) . '/public_html/storage',
	]
];
