<?php
$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';
$db_old = require __DIR__ . '/db_old.php';
$urlRules = require __DIR__ . '/url_rules.php';
$components = require __DIR__ . "/web_components.php";
$container = require __DIR__ . '/container.php';

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'language' => 'ru-RU',
    'timeZone' => 'Europe/Moscow',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'container' => $container,
    'components' => $components,
    'params' => $params,
];

if (YII_ENV != "prod") {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        'allowedIPs' => ["*"],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        'generators' => [
            'jobs' => yii\queue\gii\Generator::class,
        ],
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

return $config;
