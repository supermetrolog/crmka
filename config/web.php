<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';
$db_old = require __DIR__ . '/db_old.php';
$urlRules = require __DIR__ . '/url_rules.php';
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
    'components' => [
        'notify' => [
            'class' => app\components\NotificationService::class,
        ],
        'formatter' => [
            'class' => \yii\i18n\Formatter::className(),
            'dateFormat' => 'long',
            'currencyCode' => 'RUB',
            'decimalSeparator' => '.',
            'thousandSeparator' => ' ',
            'nullDisplay' => '',
            'numberFormatterOptions' => [
                NumberFormatter::MIN_FRACTION_DIGITS => 0,
                NumberFormatter::MAX_FRACTION_DIGITS => 2,
            ]
        ],
        'request' => [
            'enableCsrfValidation' => false,
            'cookieValidationKey' => 'p6xr64xCH9KxL1zQ7zgdZ6BzV6IH2yZl',
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
                'multipart/form-data' => 'yii\web\MultipartFormDataParser'
            ],
            'baseUrl' => ''
        ],
        'authManager' => [
            'class' => 'yii\rbac\DbManager'
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
            'enableSession' => false,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        // 'mailer' => [
        //     'class' => 'yii\swiftmailer\Mailer',
        //     'htmlLayout' => 'layouts/html',
        //     // 'useFileTransport' => true,
        //     'useFileTransport' => false,
        //     'transport' => [
        //         'class' => 'Swift_SmtpTransport',
        //         'host' => 'smtp.beget.com',
        //         'port' => 2525,
        //         'username' => 'test@supermetrolog.store',
        //         'password' => 'Studentjke2h',
        //     ],
        // ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,
        'db_old' => $db_old,
        'urlManager' => [
            'enablePrettyUrl' => true,
            'enableStrictParsing' => true,
            'showScriptName' => false,
            'rules' => $urlRules
        ],
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

return $config;
