<?php

declare(strict_types=1);

use app\kernel\common\database\interfaces\transaction\TransactionBeginnerInterface;
use app\models\ActiveQuery\NotificationChannelQuery;
use app\models\Notification\NotificationChannel;

$db     = require __DIR__ . "/db.php";
$old_db = require __DIR__ . "/db_old.php";

return [
	'singletons'  => [
		'db'                                => $db,
		'old_db'                            => $old_db,
		TransactionBeginnerInterface::class => 'db'
	],
	'definitions' => [
		NotificationChannelQuery::class => [
			'class'      => NotificationChannelQuery::class,
			'modelClass' => NotificationChannel::class
		]
	]
];
