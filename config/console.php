<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';
$db_old = require __DIR__ . '/db_old.php';

$config = [
    'id' => 'basic-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log', 'queue'],
    'controllerNamespace' => 'app\commands',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
        '@tests' => '@app/tests',
    ],
    'components' => [
        'notify' => [
            'class' => app\components\NotificationService::class,
        ],
        'queue' => [
            'class' => \yii\queue\amqp_interop\Queue::class,
            'port' => 5672,
            'user' => $params['rabbit']['user'],
            'password' => $params['rabbit']['password'],
            'queueName' => $params['rabbit']['queueName'],
            'exchangeName' => $params['rabbit']['exchangeName'],
            'driver' => yii\queue\amqp_interop\Queue::ENQUEUE_AMQP_LIB,
        ],
        'authManager' => [
            'class' => 'yii\rbac\DbManager'
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,
        'db_old' => $db_old,
    ],
    'params' => $params,
    // 'controllerMap' => [
    //     'fixture' => [ // Fixture generation command line.
    //         'class' => 'yii\faker\FixtureController',
    //     ],
    // ],
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
    ];
}

return $config;
