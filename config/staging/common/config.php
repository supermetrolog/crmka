<?php
$prod_common_this_host = "https://api.supermetrolog.ru/";
$staging_common_params = [
    'synchronizer' => [
        'this_project' => [
            'source_repo_dir_path' => "/home/user/web/api.pennylane.pro/public_html",
        ],
        'objects_project' => [
            'source_repo_dir_path' => "/home/user/web/pennylane.pro/public_html",
        ],
        'frontend_project' => [
            'source_repo_dir_path' => "/home/user/web/clients.pennylane.pro/public_html",
        ]
    ],
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

return [
    'params' => $staging_common_params,
    'components' => [
        'queue' => [
            'queueName' => $staging_common_params['rabbit']['queueName'],
            'exchangeName' => $staging_common_params['rabbit']['exchangeName']
        ]
    ]
];
