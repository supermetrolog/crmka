<?php

use yii\helpers\ArrayHelper;

if (YII_ENV === "dev") {
    return ArrayHelper::merge(require __DIR__ . "/common/web/config.php", require __DIR__ . "/dev/web/config.php");
}

if (YII_ENV === "prod") {
    return ArrayHelper::merge(require __DIR__ . "/common/web/config.php", require __DIR__ . "/prod/web/config.php");
}

if (YII_ENV === "staging") {
    return ArrayHelper::merge(require __DIR__ . "/common/web/config.php", require __DIR__ . "/staging/web/config.php");
}
