<?php

$secrets = require __DIR__ . "/../../secrets.php";

return [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=localhost;dbname=' . $secrets['importer_db']['dbname'],
    'username' => $secrets['importer_db']['username'],
    'password' => $secrets['importer_db']['password'],
    'charset' => 'utf8',
];
