<?php

defined('YII_PROJECT_ROOT') or define('YII_PROJECT_ROOT', realpath(__DIR__ . "/../"));

$secrets = require YII_PROJECT_ROOT . "/config/secrets.php";
$yii_env = $secrets['yii_env'];
$yii_debug = $secrets['yii_debug'];
if ($yii_env === null || $yii_debug === null) {
    throw new Exception('YII_ENV or YII_DEBUG not found in config');
}

defined('YII_DEBUG') or define('YII_DEBUG', $yii_debug);
defined('YII_ENV') or define('YII_ENV', $yii_env);

require YII_PROJECT_ROOT . '/vendor/autoload.php';
require YII_PROJECT_ROOT . '/vendor/yiisoft/yii2/Yii.php';

$config = require YII_PROJECT_ROOT . "/config/web.php";

(new yii\web\Application($config))->run();
