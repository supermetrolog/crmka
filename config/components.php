<?php

return [
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
    'authManager' => [
        'class' => 'yii\rbac\DbManager'
    ],
    'cache' => [
        'class' => 'yii\caching\FileCache',
    ],
    'log' => [
        'targets' => $params['logger']['targets'],
    ],
    'db' => $db,
    'db_old' => $db_old,
];
