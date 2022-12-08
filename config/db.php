<?php

$secrets = require __DIR__ . "/secrets.php";

$parameters = [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=localhost;dbname=' . $secrets['db']['dbname'],
    'username' => $secrets['db']['username'],
    'password' => $secrets['db']['password'],
    'charset' => 'utf8',
];

if (YII_ENV == "prod") {
    //Schema cache options (for production environment)
    $parameters = array_merge($parameters, [
        'enableSchemaCache' => true,
        'schemaCacheDuration' => 120,
        'schemaCache' => 'cache',
    ]);
}

return $parameters;
