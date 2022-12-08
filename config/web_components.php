<?php

$common = require __DIR__ . "/common_components.php";

$web = [
    'request' => [
        'enableCsrfValidation' => false,
        'cookieValidationKey' => 'p6xr64xCH9KxL1zQ7zgdZ6BzV6IH2yZl',
        'parsers' => [
            'application/json' => 'yii\web\JsonParser',
            'multipart/form-data' => 'yii\web\MultipartFormDataParser'
        ],
        'baseUrl' => ''
    ],
    'user' => [
        'identityClass' => 'app\models\User',
        'enableAutoLogin' => true,
        'enableSession' => false,
    ],
    'errorHandler' => [
        'errorAction' => 'site/error',
    ],
    'log' => [
        'traceLevel' => YII_DEBUG ? 3 : 0,
        'targets' => $params['logger']['targets'],
    ],
    'urlManager' => [
        'enablePrettyUrl' => true,
        'enableStrictParsing' => true,
        'showScriptName' => false,
        'rules' => $urlRules
    ],
];

return array_merge($common, $web);
