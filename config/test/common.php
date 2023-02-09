<?php

declare(strict_types=1);

return [
    'id' => 'basic-tests',
    'components' => [
        'db' => [
            'dsn' => 'mysql:host=localhost;dbname=crmka_test',
        ],
        'mailer' => [
            'useFileTransport' => true,
        ],
    ],
];
