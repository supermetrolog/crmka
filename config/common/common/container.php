<?php

use yii\db\Connection;

$common_db = require __DIR__ . "/db.php";

return [
	'singletons' => [
		Connection::class => $common_db
	]
];
