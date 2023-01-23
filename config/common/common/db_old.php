<?php

$secrets = require __DIR__ . "/../../secrets.php";

return [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=localhost;dbname=' . $secrets['db_old']['dbname'],
    'username' => $secrets['db_old']['username'],
    'password' => $secrets['db_old']['password'],
    'charset' => 'utf8',
];
