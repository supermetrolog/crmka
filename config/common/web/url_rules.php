<?php

//use app\components\router\Method;
//use app\components\router\Route;
//
//Route::controller('user')->group(static function () {
//    Route::get('/', 'index');
//    Route::get('online');
//    Route::get('<id>', 'view');
//    Route::get('<id>/sessions', 'sessions');
//
//    Route::post('/', 'create');
//    Route::post('login');
//    Route::post('logout');
//    Route::post('activity');
//    Route::post('<id>/archive', 'archive');
//    Route::post('<id>/restore', 'restore');
//
//    Route::put('<id>', 'update');
//
//    Route::delete('<id>/sessions', 'delete-sessions');
//    Route::delete('<id>', 'delete');
//});
//
//Route::controller('session')->group(static function () {
//    Route::get('/', 'index');
//    Route::delete('<id>', 'delete');
//});
//
//Route::controller('company')->alias('companies')->group(static function () {
//    Route::get('/', 'index');
//    Route::get('product-range-list');
//    Route::get('in-the-bank-list');
//    Route::get('<id>', 'view');
//
//
//    Route::post('/', 'create');
//    Route::post('<id>/logo', 'update-logo');
//
//    Route::put('<id>', 'update');
//
//    Route::delete('<id>/logo', 'delete-logo');
//    Route::delete('<id>', 'delete');
//});
//
//Route::controller('companygroup');
//Route::controller('calendar');
//
//Route::controller('request')->group(static function () {
//    Route::get('/', 'index');
//    Route::get('company-requests/<id>', 'company-requests');
//    Route::get('<id>', 'view');
//
//    Route::post('/', 'create');
//    Route::post('<id>/clone', 'clone');
//    Route::post('<id>/change-consultant', 'change-consultant');
//
//    Route::put('<id>', 'update');
//
//    Route::patch('disable');
//    Route::patch('undisable');
//});
//
//Route::controller('contact')->group(static function () {
//    Route::get('/', 'index');
//    Route::get('company-contacts/<id>', 'company-contacts');
//    Route::get('<id>', 'view');
//
//    Route::post('/', 'create');
//    Route::post('create-comment', 'create-comment');
//
//    Route::put('<id>', 'update');
//});
//
//Route::controller('contact-comment')->group(static function () {
//    Route::put('<id>', 'update');
//    Route::delete('<id>', 'delete');
//});
//
//Route::controller('timeline')->disablePluralize()->group(static function () {
//    Route::get('/', 'index');
//    Route::get('search');
//    Route::get('<id>', 'view');
//    Route::get('action-comments/<id>', 'action-comments');
//
//    Route::post('action-comment', 'add-action-comment');
//
//    Route::patch('update-step/<id>', 'update-step');
//});
//
//Route::controller('notification')->group(static function () {
//    Route::get('/', 'index');
//    Route::get('<id>/viewed-not-count', 'viewed-not-count');
//    Route::get('<id>/viewed-all', 'viewed-all');
//    Route::get('<id>/count', 'count');
//});
//
//Route::controller('calllist')->group(static function () {
//    Route::get('/', 'index');
//    Route::get('<caller_id>/viewed-not-count', 'viewed-not-count');
//    Route::get('<caller_id>/viewed-all', 'viewed-all');
//    Route::get('<caller_id>/count', 'count');
//});
//
//Route::controller('oldDb/object')->group(static function () {
//    Route::get('/', 'index');
//    Route::get('offers');
//    Route::get('offers-map');
//    Route::get('offers-count');
//    Route::get('offers-map-count');
//
//    Route::post('toggle-avito-ad/<originalId>', 'toggle-avito-ad');
//    Route::post('toggle-is-fake/<originalId>', 'toggle-is-fake');
//});
//
//Route::controller('oldDb/location')->group(static function () {
//    Route::get('region-list');
//});
//
//Route::controller('pdf/presentation')->group(static function () {
//    Route::addRoute([Method::GET], 'html');
//});
//
//Route::controller('deal');
//Route::controller('favorite-offer');
//Route::controller('company-events-log')->disablePluralize();
//
//Route::controller('complex')->disablePluralize()->group(static function () {
//    Route::get('<id>', 'view');
//});
//
//Route::controller('letter')->group(static function () {
//    Route::post('send');
//});
//
//Route::controller('archiver')->disablePluralize()->group(static function () {
//    Route::get('download');
//});
//
//Route::controller('ChatMember/chat-member')->alias('chat-members')->group(static function () {
//    Route::get('/', 'index');
//    Route::get('statistic');
//    Route::get('<id>', 'view');
//    Route::get('<id>/pinned-message', 'pinned-message');
//    Route::get('<id>/media', 'media');
//
//    Route::post('pin-message');
//    Route::post('unpin-message');
//    Route::post('<id>/called', 'called');
//});
//
//Route::controller('ChatMember/chat-member-message')->alias('chat-member-messages')->group(static function () {
//    Route::get('/', 'index');
//    Route::get('<id>', 'view');
//
//    Route::post('/', 'create');
//    Route::post('with-task', 'create-with-task');
//    Route::post('with-tasks', 'create-with-tasks');
//
//    Route::post('create-task/<id>', 'create-task');
//    Route::post('create-tasks/<id>', 'create-tasks');
//    Route::post('create-alert/<id>', 'create-alert');
//    Route::post('create-reminder/<id>', 'create-reminder');
//    Route::post('create-notification/<id>', 'create-notification');
//    Route::post('view-message/<id>', 'view-message');
//
//    Route::put('<id>', 'update');
//    Route::delete('<id>', 'delete');
//});
//
//Route::controller('media')->group(static function () {
//    Route::get('/', 'index');
//    Route::get('<id>', 'view');
//    Route::delete('<id>', 'delete');
//});
//
//Route::controller('survey')->group(static function () {
//    Route::get('/', 'index');
//    Route::get('<id>', 'view');
//    Route::get('<id>/with-questions', 'view-with-questions');
//
//    Route::post('/', 'create');
//    Route::post('with-survey-question-answer', 'create-with-survey-question-answer');
//
//    Route::put('<id>', 'update');
//    Route::put('<id>/with-survey-question-answer', 'update-with-survey-question-answer');
//    Route::delete('<id>', 'delete');
//});
//
//Route::controller('question')->group(static function () {
//    Route::get('/', 'index');
//    Route::get('with-question-answer', 'index-with-question-answer');
//    Route::get('<id>', 'view');
//
//    Route::post('/', 'create');
//    Route::post('with-question-answer', 'create-with-question-answer');
//
//    Route::put('<id>', 'update');
//    Route::delete('<id>', 'delete');
//});
//
//Route::controller('ChatMember/chat-member-message-tag')->alias('chat-member-message-tags')->crud();
//Route::controller('call')->crud();
//Route::controller('field')->crud();
//Route::controller('effect')->crud();
//Route::controller('survey-question-answer')->crud();
//
//Route::controller('equipment')->crud()->group(static function () {
//    Route::post('<id>/called')->action('called');
//});
//
//Route::controller('question-answer')->group(static function () {
//    Route::get('/', 'index');
//    Route::get('with-questions');
//    Route::get('<id>', 'view');
//
//    Route::post('/', 'create');
//
//    Route::put('<id>', 'update');
//    Route::delete('<id>', 'delete');
//});
//
//Route::controller('task')->group(static function () {
//    Route::get('/', 'index');
//    Route::get('counts');
//    Route::get('relations');
//    Route::get('<id>', 'view');
//
//    Route::put('<id>', 'update');
//    Route::delete('<id>', 'delete');
//
//    Route::post('/', 'create');
//    Route::post('for-users', 'create-for-users');
//    Route::post('change-status/<id>', 'change-status');
//
//    Route::get('<id>/history', 'history');
//
//    Route::prefix('<id>/files', static function () {
//        Route::get('/', 'files');
//        Route::post('/', 'create-files');
//        Route::delete('/', 'delete-files');
//    });
//
//    Route::prefix('<id>/comments', static function () {
//        Route::get('/', 'comments');
//        Route::post('/', 'create-comments');
//    });
//
//    Route::post('<id>/read', 'read');
//    Route::post('<id>/assign', 'assign');
//    Route::post('<id>/postpone', 'postpone');
//    Route::post('<id>/restore', 'restore');
//});
//
//Route::controller('task-tag')->group(static function () {
//    Route::get('/', 'index');
//    Route::get('all');
//    Route::get('<id>', 'view');
//
//    Route::post('/', 'create');
//
//    Route::put('<id>', 'update');
//    Route::delete('<id>', 'delete');
//});
//
//Route::controller('task-comment')->group(static function () {
//    Route::get('/', 'index');
//    Route::get('<id>', 'view');
//    Route::put('<id>', 'update');
//    Route::delete('<id>', 'delete');
//});
//
//Route::controller('task-favorite')->group(static function () {
//    Route::get('/', 'index');
//    Route::post('/', 'create');
//    Route::post('<id>/change-position', 'change-position');
//    Route::delete('<id>', 'delete');
//});
//
//Route::controller('utilities')->disablePluralize()->group(static function () {
//    Route::post('fix-land-object-purposes');
//    Route::post('reassign-consultants-to-companies');
//});
//
//Route::controller('site');
//
//return Route::buildTree();

return
    [
        [
            'class' => 'yii\rest\UrlRule',
            'controller' => 'user',
            'except' => [],
            'extraPatterns' => [
                'GET,OPTIONS' => 'index',
                'POST,OPTIONS' => 'create',
                'GET,OPTIONS online' => 'online',
                'POST,OPTIONS login' => 'login',
                'POST,OPTIONS logout' => 'logout',
                'POST,OPTIONS activity' => 'activity',
                'GET,OPTIONS <id>' => 'view',
                'PUT,OPTIONS <id>' => 'update',
                'DELETE,OPTIONS <id>' => 'delete',
                'GET,OPTIONS <id>/sessions' => 'sessions',
                'DELETE,OPTIONS <id>/sessions' => 'delete-sessions',
                'GET,OPTIONS <id>/archive' => 'archive',
                'GET,OPTIONS <id>/restore' => 'restore',
            ],
        ],
        [
            'class' => 'yii\rest\UrlRule',
            'controller' => ['companies' => 'company'],
            'except' => [],
            'extraPatterns' => [
                'GET,OPTIONS' => 'index',
                'GET,OPTIONS product-range-list' => 'product-range-list',
                'GET,OPTIONS in-the-bank-list' => 'in-the-bank-list',
                'POST,OPTIONS' => 'create',
                'GET,OPTIONS <id>' => 'view',
                'PUT,OPTIONS <id>' => 'update',
                'DELETE,OPTIONS <id>' => 'delete',
                'DELETE,OPTIONS <id>/logo' => 'delete-logo',
                'POST,OPTIONS <id>/logo' => 'update-logo',
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
                'GET,OPTIONS' => 'index',
                'GET,OPTIONS company-requests/<id>' => 'company-requests',
                'POST,OPTIONS' => 'create',
                'PATCH,OPTIONS disable/<id>' => 'disable',
                'PATCH,OPTIONS undisable/<id>' => 'undisable',
                'GET,OPTIONS <id>' => 'view',
                'PUT,OPTIONS <id>' => 'update',
                'POST,OPTIONS <id>/clone' => 'clone',
                'POST,OPTIONS <id>/change-consultant' => 'change-consultant',
            ],
        ],
        [
            'class' => 'yii\rest\UrlRule',
            'controller' => 'contact',
            'except' => [],
            'extraPatterns' => [
                'GET,OPTIONS' => 'index',
                'POST,OPTIONS' => 'create',
                'POST,OPTIONS create-comment' => 'create-comment',
                'GET,OPTIONS company-contacts/<id>' => 'company-contacts',
                'GET,OPTIONS <id>' => 'view',
                'PUT,OPTIONS <id>' => 'update',
            ],
        ],
        [
            'class' => 'yii\rest\UrlRule',
            'controller' => 'contact-comment',
            'except' => [],
            'extraPatterns' => [
                'PUT,OPTIONS <id>' => 'update',
                'DELETE,OPTIONS <id>' => 'delete',
            ],
        ],
        [
            'class' => 'yii\rest\UrlRule',
            'controller' => ['timeline' => 'timeline'],
            'except' => [],
            'extraPatterns' => [
                'GET,OPTIONS' => 'index',
                'GET,OPTIONS search' => 'search',
                'GET,OPTIONS <id>' => 'view',
                'PATCH,OPTIONS update-step/<id>' => 'update-step',
                'GET,OPTIONS action-comments/<id>' => 'action-comments',
                'POST,OPTIONS action-comment' => 'add-action-comment'
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
                'GET,OPTIONS' => 'index',
                'GET,OPTIONS offers' => 'offers',
                'GET,OPTIONS offers-map' => 'offers-map',
                'GET,OPTIONS offers-count' => 'offers-count',
                'GET,OPTIONS offers-map-count' => 'offers-map-count',
                'POST,OPTIONS toggle-avito-ad/<originalId>' => 'toggle-avito-ad',
                'POST,OPTIONS toggle-is-fake/<originalId>' => 'toggle-is-fake',
            ],
        ],
        [
            'class' => 'yii\rest\UrlRule',
            'controller' => 'oldDb/location',
            'except' => [],
            'extraPatterns' => [
                'GET,OPTIONS region-list' => 'region-list',
            ],
        ],
        [
            'class' => 'yii\rest\UrlRule',
            'controller' => 'pdf/presentation',
            'extraPatterns' => [
                'GET html' => 'html',
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
        [
            'class' => 'yii\rest\UrlRule',
            'controller' => 'letter',
            'except' => [],
            'extraPatterns' => [
                'POST,OPTIONS send' => 'send'
            ]
        ],
        [
            'class' => 'yii\rest\UrlRule',
            'controller' => ['company-events-log' => 'company-events-log'],
            'except' => [],
            'extraPatterns' => [],
        ],
        [
            'class' => 'yii\rest\UrlRule',
            'controller' => ['complex' => 'complex'],
            'extraPatterns' => [
                'GET,OPTIONS <id>' => 'view'
            ],
        ],
        [
            'class' => 'yii\rest\UrlRule',
            'controller' => ['archiver' => 'archiver'],
            'extraPatterns' => [
                'GET,OPTIONS download' => 'download'
            ],
        ],
        [
            'class' => 'yii\rest\UrlRule',
            'controller' => 'task',
            'except' => [],
            'extraPatterns' => [
                'GET,OPTIONS' => 'index',
                'GET,OPTIONS statistic' => 'statistic',
                'GET,OPTIONS counts' => 'counts',
                'GET,OPTIONS relations' => 'relations',
                'GET,OPTIONS <id>' => 'view',
                'POST,OPTIONS' => 'create',
                'POST,OPTIONS for-users' => 'create-for-users',
                'PUT,OPTIONS <id>' => 'update',
                'DELETE,OPTIONS <id>' => 'delete',
                'POST,OPTIONS change-status/<id>' => 'change-status',
                'GET,OPTIONS <id>/comments' => 'comments',
                'POST,OPTIONS <id>/comments' => 'create-comment',
                'POST,OPTIONS <id>/read' => 'read',
                'POST,OPTIONS <id>/assign' => 'assign',
                'GET,OPTIONS <id>/history' => 'history',
                'POST,OPTIONS <id>/restore' => 'restore',
                'GET,OPTIONS <id>/files' => 'files',
                'POST,OPTIONS <id>/files' => 'create-files',
                'DELETE,OPTIONS <id>/files' => 'delete-files',
                'POST,OPTIONS <id>/postpone' => 'postpone',
            ],
        ],
        [
            'class' => 'yii\rest\UrlRule',
            'controller' => ['chat-members' => 'ChatMember/chat-member'],
            'extraPatterns' => [
                'GET,OPTIONS' => 'index',
                'GET,OPTIONS statistic' => 'statistic',
                'GET,OPTIONS <id>/pinned-message' => 'pinned-message',
                'GET,OPTIONS <id>/media' => 'media',
                'POST,OPTIONS pin-message' => 'pin-message',
                'POST,OPTIONS unpin-message' => 'unpin-message',
                'POST,OPTIONS <id>/called' => 'called',
                'GET,OPTIONS <id>' => 'view',
            ],
        ],
        [
            'class' => 'yii\rest\UrlRule',
            'controller' => ['chat-member-messages' => 'ChatMember/chat-member-message'],
            'extraPatterns' => [
                'POST,OPTIONS' => 'create',
                'POST,OPTIONS with-task' => 'create-with-task',
                'POST,OPTIONS with-tasks' => 'create-with-tasks',
                'PUT,OPTIONS <id>' => 'update',
                'DELETE,OPTIONS <id>' => 'delete',
                'POST,OPTIONS create-task/<id>' => 'create-task',
                'POST,OPTIONS create-tasks/<id>' => 'create-tasks',
                'POST,OPTIONS create-alert/<id>' => 'create-alert',
                'POST,OPTIONS create-reminder/<id>' => 'create-reminder',
                'POST,OPTIONS create-notification/<id>' => 'create-notification',
                'POST,OPTIONS view-message/<id>' => 'view-message',
            ],
        ],
        [
            'class' => 'yii\rest\UrlRule',
            'controller' => ['chat-member-message-tags' => 'ChatMember/chat-member-message-tag'],
            'extraPatterns' => [
                'GET,OPTIONS' => 'index',
                'GET,OPTIONS <id>' => 'view',
                'POST,OPTIONS' => 'create',
                'PUT,OPTIONS <id>' => 'update',
                'DELETE,OPTIONS <id>' => 'delete',
            ],
        ],
        [
            'class' => 'yii\rest\UrlRule',
            'controller' => 'alert',
            'except' => [],
            'extraPatterns' => [
                'GET,OPTIONS' => 'index',
                'GET,OPTIONS <id>' => 'view',
                'POST,OPTIONS' => 'create',
                'POST,OPTIONS for-users' => 'create-for-users',
                'PUT,OPTIONS <id>' => 'update',
                'DELETE,OPTIONS <id>' => 'delete',
            ],
        ],
        [
            'class' => 'yii\rest\UrlRule',
            'controller' => 'reminder',
            'except' => [],
            'extraPatterns' => [
                'GET,OPTIONS' => 'index',
                'GET,OPTIONS statistic' => 'statistic',
                'GET,OPTIONS <id>' => 'view',
                'POST,OPTIONS' => 'create',
                'POST,OPTIONS for-users' => 'create-for-users',
                'PUT,OPTIONS <id>' => 'update',
                'DELETE,OPTIONS <id>' => 'delete',
                'POST,OPTIONS change-status/<id>' => 'change-status',
            ],
        ],
        [
            'class' => 'yii\rest\UrlRule',
            'controller' => 'media',
            'except' => [],
            'extraPatterns' => [
                'GET,OPTIONS' => 'index',
                'GET,OPTIONS <id>' => 'view',
                'DELETE,OPTIONS <id>' => 'delete',
            ],
        ],
        [
            'class' => 'yii\rest\UrlRule',
            'controller' => 'call',
            'except' => [],
            'extraPatterns' => [
                'GET,OPTIONS' => 'index',
                'GET,OPTIONS <id>' => 'view',
                'POST,OPTIONS' => 'create',
                'PUT,OPTIONS <id>' => 'update',
                'DELETE,OPTIONS <id>' => 'delete',
            ],
        ],
        [
            'class' => 'yii\rest\UrlRule',
            'controller' => 'survey',
            'except' => [],
            'extraPatterns' => [
                'GET,OPTIONS' => 'index',
                'GET,OPTIONS <id>' => 'view',
                'GET,OPTIONS <id>/with-questions' => 'view-with-questions',
                'POST,OPTIONS' => 'create',
                'POST,OPTIONS with-survey-question-answer' => 'create-with-survey-question-answer',
                'PUT,OPTIONS <id>' => 'update',
                'PUT,OPTIONS <id>/with-survey-question-answer' => 'update-with-survey-question-answer',
                'DELETE,OPTIONS <id>' => 'delete',
            ],
        ],
        [
            'class' => 'yii\rest\UrlRule',
            'controller' => 'question',
            'except' => [],
            'extraPatterns' => [
                'GET,OPTIONS' => 'index',
                'GET,OPTIONS with-question-answer' => 'index-with-question-answer',
                'GET,OPTIONS <id>' => 'view',
                'POST,OPTIONS' => 'create',
                'POST,OPTIONS with-question-answer' => 'create-with-question-answer',
                'PUT,OPTIONS <id>' => 'update',
                'DELETE,OPTIONS <id>' => 'delete',
            ],
        ],
        [
            'class' => 'yii\rest\UrlRule',
            'controller' => 'field',
            'except' => [],
            'extraPatterns' => [
                'GET,OPTIONS' => 'index',
                'GET,OPTIONS <id>' => 'view',
                'POST,OPTIONS' => 'create',
                'PUT,OPTIONS <id>' => 'update',
                'DELETE,OPTIONS <id>' => 'delete',
            ],
        ],
        [
            'class' => 'yii\rest\UrlRule',
            'controller' => 'question-answer',
            'except' => [],
            'extraPatterns' => [
                'GET,OPTIONS' => 'index',
                'GET,OPTIONS with-questions' => 'with-questions',
                'GET,OPTIONS <id>' => 'view',
                'POST,OPTIONS' => 'create',
                'PUT,OPTIONS <id>' => 'update',
                'DELETE,OPTIONS <id>' => 'delete',
            ],
        ],
        [
            'class' => 'yii\rest\UrlRule',
            'controller' => 'survey-question-answer',
            'except' => [],
            'extraPatterns' => [
                'GET,OPTIONS' => 'index',
                'GET,OPTIONS <id>' => 'view',
                'POST,OPTIONS' => 'create',
                'PUT,OPTIONS <id>' => 'update',
                'DELETE,OPTIONS <id>' => 'delete',
            ],
        ],
        [
            'class' => 'yii\rest\UrlRule',
            'controller' => 'equipment',
            'except' => [],
            'extraPatterns' => [
                'GET,OPTIONS' => 'index',
                'GET,OPTIONS <id>' => 'view',
                'POST,OPTIONS' => 'create',
                'PUT,OPTIONS <id>' => 'update',
                'DELETE,OPTIONS <id>' => 'delete',
                'POST,OPTIONS <id>/called' => 'called',
            ],
        ],
        [
            'class' => 'yii\rest\UrlRule',
            'controller' => 'task-tag',
            'except' => [],
            'extraPatterns' => [
                'GET,OPTIONS' => 'index',
                'GET,OPTIONS all' => 'all',
                'GET,OPTIONS <id>' => 'view',
                'POST,OPTIONS' => 'create',
                'PUT,OPTIONS <id>' => 'update',
                'DELETE,OPTIONS <id>' => 'delete',
            ],
        ],
        [
            'class' => 'yii\rest\UrlRule',
            'controller' => 'task-comment',
            'except' => [],
            'extraPatterns' => [
                'GET,OPTIONS' => 'index',
                'GET,OPTIONS <id>' => 'view',
                'PUT,OPTIONS <id>' => 'update',
                'DELETE,OPTIONS <id>' => 'delete',
            ],
        ],
        [
            'class' => 'yii\rest\UrlRule',
            'controller' => 'session',
            'except' => [],
            'extraPatterns' => [
                'GET,OPTIONS' => 'index',
                'DELETE,OPTIONS <id>' => 'delete',
            ],
        ],
        [
            'class' => 'yii\rest\UrlRule',
            'controller' => 'effect',
            'except' => [],
            'extraPatterns' => [
                'GET,OPTIONS' => 'index',
                'GET,OPTIONS <id>' => 'view',
                'POST,OPTIONS' => 'create',
                'PUT,OPTIONS <id>' => 'update',
                'DELETE,OPTIONS <id>' => 'delete',
            ],
        ],
        [
            'class' => 'yii\rest\UrlRule',
            'controller' => 'task-favorite',
            'except' => [],
            'extraPatterns' => [
                'GET,OPTIONS' => 'index',
                'POST,OPTIONS' => 'create',
                'DELETE,OPTIONS <id>' => 'delete',
                'POST,OPTIONS <id>/change-position' => 'change-position',
            ],
        ],
        [
            'class' => 'yii\rest\UrlRule',
            'controller' => ['utilities' => 'utilities'],
            'except' => [],
            'extraPatterns' => [
                'POST,OPTIONS fix-land-object-purposes' => 'fix-land-object-purposes',
            ],
        ]
    ];