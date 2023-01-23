<?php
$staging_common_params = require __DIR__ . "/params.php";
return [
    'params' => $staging_common_params,
    'components' => [
        'queue' => [
            'queueName' => $staging_common_params['rabbit']['queueName'],
            'exchangeName' => $staging_common_params['rabbit']['exchangeName']
        ]
    ]
];
