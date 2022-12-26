<?php

$secrets = require __DIR__ . "/../../secrets.php";
$prod_common_this_host = "https://api.supermetrolog.ru/";

return [
    'synchronizer' => [
        'this_project' => [
            'baseRepository' => [
                'dirpath' => "/home/user/web/api.pennylane.pro/public_html"
            ],
        ],
        'objects_project' => [
            'baseRepository' => [
                'dirpath' => "/home/user/web/pennylane.pro/public_html"
            ],
        ],
        'frontend_project' => [
            'baseRepository' => [
                'dirpath' => "/home/user/web/clients.pennylane.pro/public_html"
            ],
        ]
    ],
    'compressorPath' => '/home/user/scripts/pdf_compressor.py',
    'pythonPath' => '/bin/python3',
    'url' => [
        'this_host' => $prod_common_this_host,
        'image_not_found' => "{$prod_common_this_host}images/no-image.jpg",
        'empty_image' => "{$prod_common_this_host}images/empty.jpg"
    ],
    'rabbit' => [
        'queueName' => "dev_timeline_presentation_sender",
        'exchangeName' => "dev_timeline_presentation_sender_exchange"
    ]
];
