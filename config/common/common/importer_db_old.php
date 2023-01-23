<?php

$secrets = require __DIR__ . "/../../secrets.php";

return [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=localhost;dbname=' . $secrets['importer_db_old']['dbname'],
    'username' => $secrets['importer_db_old']['username'],
    'password' => $secrets['importer_db_old']['password'],
    'charset' => 'utf8',
];
