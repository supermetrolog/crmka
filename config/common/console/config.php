<?php

use yii\helpers\ArrayHelper;

return ArrayHelper::merge(
    require __DIR__ . '/../common/config.php',
    [
        'id' => 'basic-console',
        'bootstrap' => ['queue'],
        'aliases' => [
            '@tests' => '@app/tests'
        ],
        'controllerNamespace' => 'app\commands',
    ]
);
