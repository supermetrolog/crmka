<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';
$db_old = require __DIR__ . '/db_old.php';
$container = require __DIR__ . '/container.php';
$components = require __DIR__ . '/components.php';

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
    'container' => $container,
    'components' => $components,
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
