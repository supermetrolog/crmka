<?php


use app\kernel\common\database\interfaces\transaction\TransactionBeginnerInterface;

$db     = require __DIR__ . "/db.php";
$old_db = require __DIR__ . "/db_old.php";

return [
	'singletons' => [
		'db'                                => $db,
		'old_db'                            => $old_db,
		TransactionBeginnerInterface::class => 'db',
	]
];
