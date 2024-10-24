<?php

declare(strict_types=1);

use app\components\Media\Media;
use app\kernel\common\database\interfaces\transaction\TransactionBeginnerInterface;
use app\models\ActiveQuery\NotificationChannelQuery;
use app\models\Notification\NotificationChannel;

$db           = require __DIR__ . "/db.php";
$old_db       = require __DIR__ . "/db_old.php";
$media_folder = '/uploads';

return [
	'singletons'  => [
		'db'                                => $db,
		'old_db'                            => $old_db,
		TransactionBeginnerInterface::class => 'db',
		Media::class                        => [
			'class'      => Media::class,
			'diskPath'   => YII_PROJECT_ROOT . '/public_html' . $media_folder,
			'baseFolder' => $media_folder
		],
	],
	'definitions' => [
		NotificationChannelQuery::class => [
			'class'      => NotificationChannelQuery::class,
			'modelClass' => NotificationChannel::class
		]
	]
];
