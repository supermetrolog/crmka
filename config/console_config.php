<?php

use yii\helpers\ArrayHelper;

$common_console_config = require __DIR__ . "/common/console/config.php";

if (YII_ENV === "dev") {
    return ArrayHelper::merge($common_console_config, require __DIR__ . "/dev/console/config.php");
}

if (YII_ENV === "prod") {
    return ArrayHelper::merge($common_console_config, require __DIR__ . "/prod/console/config.php");
}

if (YII_ENV === "staging") {
    return ArrayHelper::merge($common_console_config, require __DIR__ . "/staging/console/config.php");
}
