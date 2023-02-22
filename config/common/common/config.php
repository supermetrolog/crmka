<?php

return [
    'basePath' => YII_PROJECT_ROOT,
    'bootstrap' => ['log'],
    'timeZone' => 'Europe/Moscow',
    'language' => 'ru-RU',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'container' => require __DIR__ . '/container.php',
    'components' => require __DIR__ . '/components.php',
    'params' => require __DIR__ . '/params.php'
];
