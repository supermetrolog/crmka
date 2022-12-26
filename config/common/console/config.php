<?php

return array_merge_recursive(
    require __DIR__ . '/../common/config.php',
    [
        'id' => 'basic-console',
        'bootstrap' => ['queue'],
        'controllerNamespace' => 'app\commands',
        'container' => require __DIR__ . '/container.php',
    ]
);
