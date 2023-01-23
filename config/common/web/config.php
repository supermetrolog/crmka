<?php

use yii\helpers\ArrayHelper;

return ArrayHelper::merge(
    require __DIR__ . '/../common/config.php',
    [
        'id' => 'basic',
        'components' => require __DIR__ . '/components.php',
    ]
);
