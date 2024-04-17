<?php


use app\kernel\common\database\connection\Connection;

$secrets = require __DIR__ . "/../../secrets.php";

return [
    'class' => Connection::class,
    'dsn' => 'mysql:host=' . $secrets['db']['host'] . ';dbname=' . $secrets['db']['dbname'],
    'username' => $secrets['db']['username'],
    'password' => $secrets['db']['password'],
    'charset' => 'utf8',
];
