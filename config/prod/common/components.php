<?php

$secrets = require __DIR__ . "/../../secrets.php";

return [
    'log' => [
        'targets' => [
            [
                'class' => 'airani\log\TelegramTarget',
                'levels' => ['error'],
                'botToken' => $secrets['tg_logger_bot']['token'], // bot token secret key
                'chatId' => $secrets['tg_logger_bot']['channel'], // chat id or channel username with @ like 12345 or @channel
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
