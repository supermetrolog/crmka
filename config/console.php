<?php

use yii\helpers\ArrayHelper;

if (YII_ENV === "dev") {
    return ArrayHelper::merge(require __DIR__ . "/common/console/config.php", require __DIR__ . "/dev/console/config.php");
}

if (YII_ENV === "prod") {
    return ArrayHelper::merge(require __DIR__ . "/common/console/config.php", require __DIR__ . "/prod/console/config.php");
}

if (YII_ENV === "staging") {
    return ArrayHelper::merge(require __DIR__ . "/common/console/config.php", require __DIR__ . "/staging/console/config.php");
}
