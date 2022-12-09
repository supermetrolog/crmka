<?php
$secrets = require __DIR__ . "/secrets.php";

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
$ftpOptionsForBackupsLoad = [
    'host' => $secrets['ftp_options_for_backups_load']['host'], // required
    'root' => '/backup/mysql/', // required
    'username' => $secrets['ftp_options_for_backups_load']['username'], // required
    'password' => $secrets['ftp_options_for_backups_load']['password'], // required
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

$parameters = [
    'adminEmail' => $secrets['adminEmail'],
    'senderEmail' => $secrets['senderEmail'],
    'senderName' => 'PENNYLANE REALTY',
    'senderUsername' => $secrets['senderUsername'],
    'senderPassword' => $secrets['senderPassword'],
    'compressorPath' => 'C:\Users\\tim-a\Desktop\pdfcompressor\pdf_compressor.py',
    'pythonPath' => 'C:\Python310\python.exe',
    'rabbit' => [
        'user' => $secrets['rabbit']['user'],
        'password' => $secrets['rabbit']['password'],
        'queueName' => "timeline_presentation_sender",
        'exchangeName' => "timeline_presentation_sender_exchange"
    ],
    'logger' => [
        'targets' => [
            [
                'class' => 'yii\log\FileTarget',
                'levels' => ['error', 'warning'],
            ],
        ]
    ],
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
        ]
    ],
    'db_backup' => [
        'ftp_client_options' => $ftpOptionsForBackupsLoad,
        'dump_tmp_dir' => __DIR__ . "/../services/backup/tmp",
        'db' => [
            'db_config_path' => __DIR__ . "/db.php"
        ],
        'db_old' => [
            'db_config_path' => __DIR__ . "/db_old.php"
        ],
    ]
];

if (YII_ENV != "dev") {
    $parameters['synchronizer']['this_project']['baseRepository']['dirpath'] = "/home/user/web/api.pennylane.pro/public_html";
    $parameters['synchronizer']['objects_project']['baseRepository']['dirpath'] = "/home/user/web/pennylane.pro/public_html";

    $parameters['compressorPath'] = '/home/user/scripts/pdf_compressor.py';
    $parameters['pythonPath'] = '/bin/python3';
}

if (YII_ENV == "stage") {

    $parameters['rabbit']['queueName'] = "dev_timeline_presentation_sender";
    $parameters['rabbit']['exchangeName'] = "dev_timeline_presentation_sender_exchange";
}

if (YII_ENV == "prod") {
    $parameters['logger']['targets'][] = [
        'class' => 'airani\log\TelegramTarget',
        'levels' => ['error'],
        'botToken' => $secrets['tg_logger_bot']['token'], // bot token secret key
        'chatId' => $secrets['tg_logger_bot']['channel'], // chat id or channel username with @ like 12345 or @channel
    ];
}

return $parameters;
