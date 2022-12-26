<?php

$secrets = require __DIR__ . "/../../secrets.php";

return [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=localhost;dbname=' . $secrets['db']['dbname'],
    'username' => $secrets['db']['username'],
    'password' => $secrets['db']['password'],
    'charset' => 'utf8',
];
