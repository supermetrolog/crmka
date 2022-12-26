<?php

$secrets = require __DIR__ . "/../../secrets.php";

$ftpOptionsForSyncThisProject = [
    'host' => $secrets['ftp_options_for_sync_this_project']['host'], // required
    'root' => '/', // required
    'username' => $secrets['ftp_options_for_sync_this_project']['username'], // required
    'password' => $secrets['ftp_options_for_sync_this_project']['password'], // required
    'port' => 21,
    'ssl' => false,
    'timeout' => 90,
    'utf8' => false,
    'passive' => true,
    'transferMode' => FTP_BINARY,
    'systemType' => "unix", // 'windows' or 'unix'
    'ignorePassiveAddress' => true, // true or false
    'timestampsOnUnixListingsEnabled' => false, // true or false
    'recurseManually' => true // true
];

$ftpOptionsForSyncObjectsProject = [
    'host' => $secrets['ftp_options_for_sync_objects_project']['host'], // required
    'root' => '/', // required
    'username' => $secrets['ftp_options_for_sync_objects_project']['username'], // required
    'password' => $secrets['ftp_options_for_sync_objects_project']['password'], // required
    'port' => 21,
    'ssl' => false,
    'timeout' => 90,
    'utf8' => false,
    'passive' => true,
    'transferMode' => FTP_BINARY,
    'systemType' => "unix", // 'windows' or 'unix'
    'ignorePassiveAddress' => true, // true or false
    'timestampsOnUnixListingsEnabled' => false, // true or false
    'recurseManually' => true // true
];
$ftpOptionsForSyncFrontendProject = [
    'host' => $secrets['ftp_options_for_sync_frontend_project']['host'], // required
    'root' => '/', // required
    'username' => $secrets['ftp_options_for_sync_frontend_project']['username'], // required
    'password' => $secrets['ftp_options_for_sync_frontend_project']['password'], // required
    'port' => 21,
    'ssl' => false,
    'timeout' => 90,
    'utf8' => false,
    'passive' => true,
    'transferMode' => FTP_BINARY,
    'systemType' => "unix", // 'windows' or 'unix'
    'ignorePassiveAddress' => true, // true or false
    'timestampsOnUnixListingsEnabled' => false, // true or false
    'recurseManually' => true // true
];
return [
    'synchronizer' => [
        'this_project' => [
            'baseRepository' => [
                'dirpath' => __DIR__ . "/../public_html/uploads"
            ],
            'targetRepository' => [
                'dirpath' => ".",
                'ftp' => $ftpOptionsForSyncThisProject,
            ],
            'alreadySynchronizedRepository' => [
                'filename' => 'sync-file.data',
                'repository' => [
                    'dirpath' => ".",
                    'ftp' => $ftpOptionsForSyncThisProject
                ]
            ]
        ],
        'objects_project' => [
            'baseRepository' => [
                'dirpath' => "/home/user/web/pennylane.pro/public_html"
            ],
            'targetRepository' => [
                'dirpath' => ".",
                'ftp' => $ftpOptionsForSyncObjectsProject,
            ],
            'alreadySynchronizedRepository' => [
                'filename' => 'sync-file.data',
                'repository' => [
                    'dirpath' => ".",
                    'ftp' => $ftpOptionsForSyncObjectsProject
                ]
            ]
        ],
        'frontend_project' => [
            'baseRepository' => [
                'dirpath' => __DIR__ . "/../public_html/uploads"
            ],
            'targetRepository' => [
                'dirpath' => ".",
                'ftp' => $ftpOptionsForSyncFrontendProject,
            ],
            'alreadySynchronizedRepository' => [
                'filename' => 'sync-file.data',
                'repository' => [
                    'dirpath' => ".",
                    'ftp' => $ftpOptionsForSyncFrontendProject
                ]
            ]
        ]
    ],
    'compressorPath' => 'C:\Users\\tim-a\Desktop\pdfcompressor\pdf_compressor.py',
    'pythonPath' => 'C:\Python310\python.exe',
    'rabbit' => [
        'queueName' => "timeline_presentation_sender",
        'exchangeName' => "timeline_presentation_sender_exchange"
    ],
];
