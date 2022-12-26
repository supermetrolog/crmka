<?php

return array_merge_recursive(
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
