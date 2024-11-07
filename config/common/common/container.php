<?php

declare(strict_types=1);

use app\components\EventManager;
use app\kernel\common\database\interfaces\transaction\TransactionBeginnerInterface;
use app\models\ActiveQuery\NotificationChannelQuery;
use app\models\Notification\NotificationChannel;

$db     = require __DIR__ . "/db.php";
$old_db = require __DIR__ . "/db_old.php";

return [
	'singletons'  => [
		'db'                                => $db,
		'old_db'                            => $old_db,
		TransactionBeginnerInterface::class => 'db',
		EventManager::class                 => [
			'class'  => EventManager::class,
			'config' => require YII_PROJECT_ROOT . '/config/common/common/events.php'
		]
	],
	'definitions' => [
		NotificationChannelQuery::class => [
			'class'      => NotificationChannelQuery::class,
			'modelClass' => NotificationChannel::class
		]
	]
];
