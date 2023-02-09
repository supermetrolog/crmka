<?php

use yii\helpers\ArrayHelper;

if (YII_ENV === "test") {
    return ArrayHelper::merge(require __DIR__ . "/common/common/config.php", require __DIR__ . "/test/web.php");
} else {
    die('test access with only dev env');
}
