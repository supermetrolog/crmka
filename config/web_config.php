<?php

use yii\helpers\ArrayHelper;

$common_web_config = require __DIR__ . "/common/web/config.php";

if (YII_ENV === "dev") {
    return ArrayHelper::merge($common_web_config, require __DIR__ . "/dev/web/config.php");
}

if (YII_ENV === "prod") {
    return ArrayHelper::merge($common_web_config, require __DIR__ . "/prod/web/config.php");
}

if (YII_ENV === "staging") {
    return ArrayHelper::merge($common_web_config, require __DIR__ . "/staging/web/config.php");
}
