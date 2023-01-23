<?php

use yii\helpers\ArrayHelper;

return ArrayHelper::merge(
    require __DIR__ . "/../common/config.php",
    [
        'bootstrap' => ['debug'],
        'modules' => [
            'debug' => [
                'class' => 'yii\debug\Module',
                'allowedIPs' => ["*"],
            ]
        ]
    ]
);
