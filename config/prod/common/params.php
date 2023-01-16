<?php

$secrets = require __DIR__ . "/../../secrets.php";
$prod_common_this_host = "https://api.pennylane.pro/";

return [
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
];
