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
                'POST login' => 'login',
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
    ];
