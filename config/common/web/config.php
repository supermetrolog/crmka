<?php

return array_merge_recursive(
    require __DIR__ . '/../common/config.php',
    [
        'id' => 'basic',
        'components' => require __DIR__ . '/components.php',
    ]
);
