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
            'controller' => 'calendar',
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
                'GET,OPTIONS action-comments/<id>' => 'action-comments',
                'POST,OPTIONS send-objects' => 'send-objects',
            ],
        ],
        [
            'class' => 'yii\rest\UrlRule',
            'controller' => 'notification',
            'except' => [],
            'extraPatterns' => [
                'GET,OPTIONS <id>/viewed-not-count' => 'viewed-not-count',
                'GET,OPTIONS <id>/viewed-all' => 'viewed-all',
                'GET,OPTIONS <id>/count' => 'count',
            ],
        ],
        [
            'class' => 'yii\rest\UrlRule',
            'controller' => 'calllist',
            'except' => [],
            'extraPatterns' => [
                'GET,OPTIONS <caller_id>/viewed-not-count' => 'viewed-not-count',
                'GET,OPTIONS <caller_id>/viewed-all' => 'viewed-all',
                'GET,OPTIONS <caller_id>/count' => 'count',
            ],
        ],
        [
            'class' => 'yii\rest\UrlRule',
            'controller' => 'pdf',
            'except' => [],
            'extraPatterns' => [
                'GET fuck' => 'fuck',
            ],
        ],
        [
            'class' => 'yii\rest\UrlRule',
            'controller' => 'site',
            'except' => [],

        ],
        [
            'class' => 'yii\rest\UrlRule',
            'controller' => 'oldDb/object',
            'except' => [],
            'extraPatterns' => [
                'GET,OPTIONS offers' => 'offers',
            ],
        ],
        [
            'class' => 'yii\rest\UrlRule',
            'controller' => 'pdf/presentation',
            'extraPatterns' => [
                'GET fuck' => 'fuck',
            ],
        ],
        [
            'class' => 'yii\rest\UrlRule',
            'controller' => 'deal',
            'except' => [],
        ],
        [
            'class' => 'yii\rest\UrlRule',
            'controller' => 'favorite-offer',
            'except' => [],
        ],
    ];
