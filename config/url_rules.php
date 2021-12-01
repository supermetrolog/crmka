<?php
return
    [
        [
            'class' => 'yii\rest\UrlRule',
            'controller' => 'user',
            'except' => [],
            // 'patterns' => [
            // 'PATCH /update/' => 'fuck',
            // ],
            'extraPatterns' => [
                'POST,OPTIONS login' => 'login',
                'POST,OPTIONS logout' => 'logout',
            ],
        ],
        [
            'class' => 'yii\rest\UrlRule',
            'controller' => 'company',
            'except' => [],
            'extraPatterns' => [
                'GET search' => 'search',
                'GET product-range-list' => 'product-range-list',
                'GET in-the-bank-list' => 'in-the-bank-list',
            ],
        ],
        [
            'class' => 'yii\rest\UrlRule',
            'controller' => 'companygroup',
            'except' => [],
        ],
        [
            'class' => 'yii\rest\UrlRule',
            'controller' => 'request',
            'except' => [],
            'extraPatterns' => [
                'GET company-requests/<id>' => 'company-requests',
                'GET search' => 'search',
            ],
        ],
        [
            'class' => 'yii\rest\UrlRule',
            'controller' => 'contact',
            'except' => [],
            'extraPatterns' => [
                'GET company-contacts/<id>' => 'company-contacts',
                'POST create-comment' => 'create-comment',
                'OPTIONS create-comment' => 'options',
            ],
        ],
        [
            'class' => 'yii\rest\UrlRule',
            'controller' => ['timeline' => 'timeline'],
            'except' => [],
            'extraPatterns' => [
                'GET /' => 'index',
                'PATCH update-step/<id>' => 'update-step',
                'OPTIONS update-step/<id>' => 'options',
                'POST,OPTIONS add-objects/<id>' => 'add-objects',
                'GET search' => 'search',
            ],
        ],
        [
            'class' => 'yii\rest\UrlRule',
            'controller' => 'notification',
            'except' => [],
            'extraPatterns' => [
                'GET <id>' => 'index',
                'GET,OPTIONS new/<id>' => 'new',
                'GET <id>/viewed' => 'viewed',
            ],
        ],
        [
            'class' => 'yii\rest\UrlRule',
            'controller' => 'calllist',
            'except' => [],
            'extraPatterns' => [
                'GET <id>' => 'index',
                'GET <id>/viewed' => 'viewed',
            ],
        ],
    ];
