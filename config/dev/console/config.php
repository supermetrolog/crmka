<?php

use yii\helpers\ArrayHelper;

return ArrayHelper::merge(
    require __DIR__ . "/../common/config.php",
    [
        'bootstrap' => ['gii'],
        'modules' => [
            'gii' => [
                'class' => 'yii\gii\Module',
            ]
        ]
    ]
);
