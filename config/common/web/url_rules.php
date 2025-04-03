<?php

use app\components\router\Method;
use app\components\router\Route;

Route::controller('user')->group(static function () {
    Route::get('/', 'index');
    Route::get('online');
    Route::get('<id>', 'view');
    Route::get('<id>/sessions', 'sessions');

    Route::post('/', 'create');
    Route::post('login');
    Route::post('logout');
    Route::post('activity');
    Route::post('<id>/archive', 'archive');
    Route::post('<id>/restore', 'restore');

    Route::put('<id>', 'update');

    Route::delete('<id>/sessions', 'delete-sessions');
    Route::delete('<id>', 'delete');
});

Route::controller('session')->group(static function () {
    Route::get('/', 'index');
    Route::delete('<id>', 'delete');
});

Route::controller('company')->alias('companies')->group(static function () {
    Route::get('/', 'index');
    Route::get('product-range-list');
    Route::get('in-the-bank-list');
    Route::get('<id>', 'view');


    Route::post('/', 'create');
    Route::post('<id>/logo', 'update-logo');

    Route::put('<id>', 'update');

    Route::delete('<id>/logo', 'delete-logo');
    Route::delete('<id>', 'delete');
});

Route::controller('companygroup');
Route::controller('calendar');

Route::controller('request')->group(static function () {
    Route::get('/', 'index');
    Route::get('company-requests/<id>', 'company-requests');
    Route::get('<id>', 'view');

    Route::post('/', 'create');
    Route::post('<id>/clone', 'clone');
    Route::post('<id>/change-consultant', 'change-consultant');

    Route::put('<id>', 'update');

    Route::patch('disable');
    Route::patch('undisable');
});

Route::controller('contact')->group(static function () {
    Route::get('/', 'index');
    Route::get('company-contacts/<id>', 'company-contacts');
    Route::get('<id>', 'view');

    Route::post('/', 'create');
    Route::post('create-comment', 'create-comment');

    Route::put('<id>', 'update');
});

Route::controller('contact-comment')->group(static function () {
    Route::put('<id>', 'update');
    Route::delete('<id>', 'delete');
});

Route::controller('timeline')->disablePluralize()->group(static function () {
    Route::get('/', 'index');
    Route::get('search');
    Route::get('<id>', 'view');
    Route::get('action-comments/<id>', 'action-comments');

    Route::post('action-comment', 'add-action-comment');

    Route::patch('update-step/<id>', 'update-step');
});

Route::controller('notification')->group(static function () {
    Route::get('/', 'index');
    Route::get('<id>/viewed-not-count', 'viewed-not-count');
    Route::get('<id>/viewed-all', 'viewed-all');
    Route::get('<id>/count', 'count');
});

Route::controller('calllist')->group(static function () {
    Route::get('/', 'index');
    Route::get('<caller_id>/viewed-not-count', 'viewed-not-count');
    Route::get('<caller_id>/viewed-all', 'viewed-all');
    Route::get('<caller_id>/count', 'count');
});

Route::controller('oldDb/object')->group(static function () {
    Route::get('/', 'index');
    Route::get('offers');
    Route::get('offers-map');
    Route::get('offers-count');
    Route::get('offers-map-count');

    Route::post('toggle-avito-ad/<originalId>', 'toggle-avito-ad');
    Route::post('toggle-is-fake/<originalId>', 'toggle-is-fake');
});

Route::controller('oldDb/location')->group(static function () {
    Route::get('region-list');
});

Route::controller('pdf/presentation')->group(static function () {
    Route::addRoute([Method::GET], 'html');
});

Route::controller('deal');
Route::controller('favorite-offer');
Route::controller('company-events-log')->disablePluralize();

Route::controller('complex')->disablePluralize()->group(static function () {
    Route::get('<id>', 'view');
});

Route::controller('letter')->group(static function () {
    Route::post('send');
});

Route::controller('archiver')->disablePluralize()->group(static function () {
    Route::get('download');
});

Route::controller('ChatMember/chat-member')->alias('chat-members')->group(static function () {
    Route::get('/', 'index');
    Route::get('statistic');
    Route::get('<id>', 'view');
    Route::get('<id>/pinned-message', 'pinned-message');
    Route::get('<id>/media', 'media');

    Route::post('pin-message');
    Route::post('unpin-message');
    Route::post('<id>/called', 'called');
});

Route::controller('ChatMember/chat-member-message')->alias('chat-member-messages')->group(static function () {
    Route::get('/', 'index');
    Route::get('<id>', 'view');

    Route::post('/', 'create');
    Route::post('with-task', 'create-with-task');
    Route::post('with-tasks', 'create-with-tasks');

    Route::post('create-task/<id>', 'create-task');
    Route::post('create-tasks/<id>', 'create-tasks');
    Route::post('create-alert/<id>', 'create-alert');
    Route::post('create-reminder/<id>', 'create-reminder');
    Route::post('create-notification/<id>', 'create-notification');
    Route::post('view-message/<id>', 'view-message');

    Route::put('<id>', 'update');
    Route::delete('<id>', 'delete');
});

Route::controller('media')->group(static function () {
    Route::get('/', 'index');
    Route::get('<id>', 'view');
    Route::delete('<id>', 'delete');
});

Route::controller('survey')->group(static function () {
    Route::get('/', 'index');
    Route::get('<id>', 'view');
    Route::get('<id>/with-questions', 'view-with-questions');

    Route::post('/', 'create');
    Route::post('with-survey-question-answer', 'create-with-survey-question-answer');

    Route::put('<id>', 'update');
    Route::put('<id>/with-survey-question-answer', 'update-with-survey-question-answer');
    Route::delete('<id>', 'delete');
});

Route::controller('question')->group(static function () {
    Route::get('/', 'index');
    Route::get('with-question-answer', 'index-with-question-answer');
    Route::get('<id>', 'view');

    Route::post('/', 'create');
    Route::post('with-question-answer', 'create-with-question-answer');

    Route::put('<id>', 'update');
    Route::delete('<id>', 'delete');
});

Route::controller('ChatMember/chat-member-message-tag')->alias('chat-member-message-tags')->crud();
Route::controller('call')->crud();
Route::controller('field')->crud();
Route::controller('effect')->crud();
Route::controller('survey-question-answer')->crud();

Route::controller('equipment')->crud()->group(static function () {
    Route::post('<id>/called')->action('called');
});

Route::controller('question-answer')->group(static function () {
    Route::get('/', 'index');
    Route::get('with-questions');
    Route::get('<id>', 'view');

    Route::post('/', 'create');

    Route::put('<id>', 'update');
    Route::delete('<id>', 'delete');
});

Route::controller('task')->group(static function () {
    Route::get('/', 'index');
    Route::get('counts');
    Route::get('relations');
    Route::get('<id>', 'view');

    Route::put('<id>', 'update');
    Route::delete('<id>', 'delete');

    Route::post('/', 'create');
    Route::post('for-users', 'create-for-users');
    Route::post('change-status/<id>', 'change-status');

    Route::get('<id>/history', 'history');

    Route::prefix('<id>/files', static function () {
        Route::get('/', 'files');
        Route::post('/', 'create-files');
        Route::delete('/', 'delete-files');
    });

    Route::prefix('<id>/comments', static function () {
        Route::get('/', 'comments');
        Route::post('/', 'create-comments');
    });

    Route::post('<id>/read', 'read');
    Route::post('<id>/assign', 'assign');
    Route::post('<id>/postpone', 'postpone');
    Route::post('<id>/restore', 'restore');
});

Route::controller('task-tag')->group(static function () {
    Route::get('/', 'index');
    Route::get('all');
    Route::get('<id>', 'view');

    Route::post('/', 'create');

    Route::put('<id>', 'update');
    Route::delete('<id>', 'delete');
});

Route::controller('task-comment')->group(static function () {
    Route::get('/', 'index');
    Route::get('<id>', 'view');
    Route::put('<id>', 'update');
    Route::delete('<id>', 'delete');
});

Route::controller('task-favorite')->group(static function () {
    Route::get('/', 'index');
    Route::post('/', 'create');
    Route::post('<id>/change-position', 'change-position');
    Route::delete('<id>', 'delete');
});

Route::controller('utilities')->disablePluralize()->group(static function () {
    Route::post('fix-land-object-purposes');
    Route::post('reassign-consultants-to-companies');
});

Route::controller('site');

return Route::buildTree();
