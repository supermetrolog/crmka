<?php

$secrets = require __DIR__ . "/../../secrets.php";

$ftpOptionsForBackupsLoad = [
	'host'     => $secrets['ftp_options_for_backups_load']['host'], // required
	'root'     => '/backup/mysql/', // required
	'username' => $secrets['ftp_options_for_backups_load']['username'], // required
	'password' => $secrets['ftp_options_for_backups_load']['password'], // required
];

$common_thisHost = "http://crmka_nginx/";

return [
	'company_phone'      => '+7 (495) 150-03-23',
	'adminEmail'         => $secrets['adminEmail'],
	'senderEmail'        => $secrets['senderEmail'],
	'senderName'         => 'RAYS ARMA',
	'senderUsername'     => $secrets['senderUsername'],
	'senderPassword'     => $secrets['senderPassword'],
	'compressorPath'     => YII_PROJECT_ROOT . "/pdf_compressor.py",
	'pythonPath'         => 'C:\Python310\python.exe',
	'rabbit'             => [
		'user'         => $secrets['rabbit']['user'],
		'password'     => $secrets['rabbit']['password'],
		'queueName'    => "timeline_presentation_sender",
		'exchangeName' => "timeline_presentation_sender_exchange",
		'notify'       => [
			'queueName'    => 'notifications_queue',
			'exchangeName' => 'notifications_exchange',
		]
	],
	'pdf'                => [
		'tmp_dir' => YII_PROJECT_ROOT . "/runtime/pdf_tmp",
	],
	'synchronizer'       => [
		'this_project'     => [
			'source_repo_dir_path'       => YII_PROJECT_ROOT . "/public_html/uploads",
			'target_repo_ftp_params'     => [
				'host'     => $secrets['ftp_options_for_sync_this_project']['host'],
				'root'     => "/",
				'username' => $secrets['ftp_options_for_sync_this_project']['username'],
				'password' => $secrets['ftp_options_for_sync_this_project']['password']
			],
			'already_sync_repo_filename' => "sync-file.data",
		],
		'objects_project'  => [
			'source_repo_dir_path'       => YII_PROJECT_ROOT . "/public_html/uploads",
			'target_repo_ftp_params'     => [
				'host'     => $secrets['ftp_options_for_sync_objects_project']['host'],
				'root'     => "/",
				'username' => $secrets['ftp_options_for_sync_objects_project']['username'],
				'password' => $secrets['ftp_options_for_sync_objects_project']['password']
			],
			'already_sync_repo_filename' => "sync-file.data",
		],
		'frontend_project' => [
			'source_repo_dir_path'       => YII_PROJECT_ROOT . "/public_html/uploads",
			'target_repo_ftp_params'     => [
				'host'     => $secrets['ftp_options_for_sync_frontend_project']['host'],
				'root'     => "/",
				'username' => $secrets['ftp_options_for_sync_frontend_project']['username'],
				'password' => $secrets['ftp_options_for_sync_frontend_project']['password']
			],
			'already_sync_repo_filename' => "sync-file.data",
		],
	],
	'db_backup'          => [
		'ftp_client_options' => $ftpOptionsForBackupsLoad,
		'db'                 => [
			'dump_tmp_dir'   => YII_PROJECT_ROOT . "/runtime/backup_tmp/db",
			'db_config_path' => __DIR__ . "/db.php"
		],
		'db_old'             => [
			'dump_tmp_dir'   => YII_PROJECT_ROOT . "/runtime/backup_tmp/db_old",
			'db_config_path' => __DIR__ . "/db_old.php"
		],
	],
	'db_importer'        => [
		'workdir' => "/home/user/backup/mysql",
		'db'      => [
			'db_config_path' => __DIR__ . "/importer_db.php"
		],
		'db_old'  => [
			'db_config_path' => __DIR__ . "/importer_db_old.php"
		],
	],
	'ssh'                => [
		'reserve_server' => [
			'username' => $secrets['ssh']['reserve_server']['username'],
			'password' => $secrets['ssh']['reserve_server']['password'],
			'host'     => $secrets['ssh']['reserve_server']['host'],
		]
	],
	'url'                => [
		'objects'           => "https://pennylane.pro/",
		'objects_watermark' => "https://pennylane.pro/system/controllers/photos/watermark.php/",
		'this_host'         => $common_thisHost,
		'image_not_found'   => "{$common_thisHost}images/no-image.jpg",
		'empty_image'       => "{$common_thisHost}images/empty.jpg"
	],
	'media'              => [
		'baseFolder' => '/uploads',
		'diskPath'   => YII_PROJECT_ROOT . '/public_html' . '/uploads',
		'baseUrl'    => $common_thisHost,
	],
	'router'             => [
		'cacheFilePath'    => YII_PROJECT_ROOT . '/config/common/web/url_rules.php',
		'routerConfigPath' => YII_PROJECT_ROOT . '/config/common/web/routes.php',
	],
	'allowed_office_ips' => $secrets['allowed_office_ips']
];
