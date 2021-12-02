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
                'GET,OPTIONS search' => 'search',
                'GET,OPTIONS product-range-list' => 'product-range-list',
                'GET,OPTIONS in-the-bank-list' => 'in-the-bank-list',
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
                'GET,OPTIONS company-requests/<id>' => 'company-requests',
                'GET,OPTIONS search' => 'search',
            ],
        ],
        [
            'class' => 'yii\rest\UrlRule',
            'controller' => 'contact',
            'except' => [],
            'extraPatterns' => [
                'GET,OPTIONS company-contacts/<id>' => 'company-contacts',
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
                'GET,OPTIONS search' => 'search',
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
                'GET,OPTIONS <id>/viewed' => 'viewed',
            ],
        ],
    ];
