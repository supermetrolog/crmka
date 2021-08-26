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
                'GET logout' => 'logout',
            ],
        ],
        [
            'class' => 'yii\rest\UrlRule',
            'controller' => 'company',
            'except' => [],
            'extraPatterns' => [
                'GET search' => 'search',
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
            'controller' => ['timeline' => 'timeline'],
            'except' => [],
            'extraPatterns' => [
                'GET /' => 'index',
                'GET search' => 'search',
            ],
        ],
        [
            'class' => 'yii\rest\UrlRule',
            'controller' => 'notification',
            'except' => [],
            'extraPatterns' => [
                'GET <id>' => 'index',
                'GET new/<id>' => 'new',
                'GET <id>/viewed' => 'viewed',
            ],
        ],
    ];
