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

$common_thisHost = "http://crmka/";

return [
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
    'db_backup' => [
        'ftp_client_options' => $ftpOptionsForBackupsLoad,
        'dump_tmp_dir' => __DIR__ . "/../services/backup/tmp",
        'db' => [
            'db_config_path' => __DIR__ . "/db.php"
        ],
        'db_old' => [
            'db_config_path' => __DIR__ . "/db_old.php"
        ],
    ],
    'db_importer' => [
        'workdir' => "/home/user/backup/mysql",
        'db' => [
            'db_config_path' => __DIR__ . "/importer_db.php"
        ],
        'db_old' => [
            'db_config_path' => __DIR__ . "/importer_db_old.php"
        ],
    ],
    'ssh' => [
        'reserve_server' => [
            'username' => $secrets['ssh']['reserve_server']['username'],
            'password' => $secrets['ssh']['reserve_server']['password'],
            'host' => $secrets['ssh']['reserve_server']['host'],
        ]
    ],
    'url' => [
        'objects' => "https://pennylane.pro/",
        'this_host' => $common_thisHost,
        'image_not_found' => "{$common_thisHost}images/no-image.jpg",
        'empty_image' => "{$common_thisHost}images/empty.jpg"
    ],
];
