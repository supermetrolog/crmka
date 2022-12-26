<?php

return [
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
    'urlManager' => [
        'enablePrettyUrl' => true,
        'enableStrictParsing' => true,
        'showScriptName' => false,
        'rules' => require __DIR__ . "/url_rules.php"
    ],
];
