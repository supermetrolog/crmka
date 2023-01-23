<?php

$secrets = require __DIR__ . "/../../secrets.php";

return [
    'log' => [
        'targets' => [
            [
                'class' => 'airani\log\TelegramTarget',
                'levels' => ['error', 'warning'],
                'botToken' => $secrets['tg_logger_bot']['token'], // bot token secret key
                'chatId' => $secrets['tg_logger_bot']['channel'], // chat id or channel username with @ like 12345 or @channel
                'except' => [
                    'yii\web\HttpException:401',
                    'yii\web\HttpException:404'
                ]
            ]
        ]
    ],

    'db' => [
        'enableSchemaCache' => true,
        'schemaCacheDuration' => 120,
        'schemaCache' => 'cache',
    ],
    'db_old' => [
        'enableSchemaCache' => true,
        'schemaCacheDuration' => 120,
        'schemaCache' => 'cache',
    ]
];
