<?php

use app\components\router\interfaces\RouteInterface;
use app\components\router\interfaces\RouterInterface;
use app\components\router\Method;

return static function (RouterInterface $router) {
	$router->controller('companygroup');
	$router->controller('calendar');
	$router->controller('deal');
	$router->controller('favorite-offer');
	$router->controller('company-events-log')->disablePluralize();

	$router->controller('ChatMember/chat-member-message-tag')->alias('chat-member-message-tags')->crud();
	$router->controller('call')->crud();
	$router->controller('field')->crud();
	$router->controller('effect')->crud();
	$router->controller('survey-question-answer')->crud();
	$router->controller('site');

	$router->controller('user')->group(static function (RouteInterface $route) {
		$route->get()->action('index');
		$route->get('online');

		$route->post()->action('create');
		$route->post('login');
		$route->post('logout');
		$route->post('activity');

		$route->prefix('<id>', static function (RouteInterface $route) {
			$route->get()->action('view');
			$route->get('sessions');

			$route->post('archive');
			$route->post('restore');

			$route->put()->action('update');

			$route->delete()->action('delete-sessions');
			$route->delete('sessions', 'delete-sessions');
		});
	});

	$router->controller('session')->crud(['index', 'delete']);

	$router->controller('company')->alias('companies')->group(static function (RouteInterface $route) {
		$route->get()->action('index');
		$route->get('product-range-list');
		$route->get('in-the-bank-list');

		$route->post()->action('create');

		$route->prefix('<id>', static function (RouteInterface $route) {
			$route->get()->action('view');

			$route->put()->action('update');

			$route->post('disable');
			$route->post('enable');

			$route->post('logo', 'update-logo');
			$route->delete('logo', 'delete-logo');

			$route->delete()->action('delete');
		});
	});

	$router->controller('request')->group(static function (RouteInterface $route) {
		$route->get()->action('index');
		$route->get('company-requests/<id>', 'company-requests');

		$route->post()->action('create');

		$route->patch('disable');
		$route->patch('undisable');

		$route->prefix('<id>', static function (RouteInterface $route) {
			$route->get()->action('view');
			$route->post('clone');
			$route->post('change-consultant');
			$route->put()->action('update');
		});
	});

	$router->controller('contact')->group(static function (RouteInterface $route) {
		$route->get()->action('index');
		$route->get('company-contacts/<id>', 'company-contacts');

		$route->post()->action('create');
		$route->post('create-comment');

		$route->prefix('<id>', static function (RouteInterface $route) {
			$route->get()->action('view');
			$route->put()->action('update');
		});
	});

	$router->controller('contact-comment')->crud(['update', 'delete']);

	$router->controller('timeline')->disablePluralize()->group(static function (RouteInterface $route) {
		$route->get()->action('index');
		$route->get('search');
		$route->get('<id>', 'view');
		$route->get('action-comments/<id>', 'action-comments');

		$route->post()->action('create');
		$route->post('action-comment', 'add-action-comment');

		$route->patch('update-step/<id>', 'update-step');
	});

	$router->controller('notification')->group(static function (RouteInterface $route) {
		$route->get()->action('index');

		$route->prefix('<id>', static function (RouteInterface $route) {
			$route->get('viewed-not-count');
			$route->get('viewed-all');
			$route->get('count');
		});
	});

	$router->controller('calllist')->group(static function (RouteInterface $route) {
		$route->get()->action('index');

		$route->prefix('<caller_id>', static function (RouteInterface $route) {
			$route->get('viewed-not-count');
			$route->get('viewed-all');
			$route->get('count');
		});
	});

	$router->controller('oldDb/object')->group(static function (RouteInterface $route) {
		$route->get()->action('index');
		$route->get('offers');
		$route->get('offers-map');
		$route->get('offers-count');
		$route->get('offers-map-count');

		$route->post('toggle-avito-ad/<originalId>', 'toggle-avito-ad');
		$route->post('toggle-is-fake/<originalId>', 'toggle-is-fake');
	});

	$router->controller('oldDb/location')->group(static function (RouteInterface $route) {
		$route->get('region-list');
	});

	$router->controller('pdf/presentation')->group(static function (RouteInterface $route) {
		$route->addRule([Method::GET], 'html');
	});

	$router->controller('complex')->disablePluralize()->group(static function (RouteInterface $route) {
		$route->get('<id>', 'view');
	});

	$router->controller('letter')->group(static function (RouteInterface $route) {
		$route->post('send');
	});

	$router->controller('archiver')->disablePluralize()->group(static function (RouteInterface $route) {
		$route->get('download');
	});

	$router->controller('ChatMember/chat-member')->alias('chat-members')->group(static function (RouteInterface $route) {
		$route->get()->action('index');
		$route->get('statistic');

		$route->post('pin-message');
		$route->post('unpin-message');

		$route->prefix('<id>', static function (RouteInterface $route) {
			$route->get()->action('view');
			$route->get('pinned-message');
			$route->get('media');

			$route->post('called');
		});
	});

	$router->controller('ChatMember/chat-member-message')->alias('chat-member-messages')->group(static function (RouteInterface $route) {
		$route->get()->action('index');

		$route->post()->action('create');
		$route->post('with-task', 'create-with-task');
		$route->post('with-tasks', 'create-with-tasks');

		$route->post('create-task/<id>', 'create-task');
		$route->post('create-tasks/<id>', 'create-tasks');
		$route->post('create-alert/<id>', 'create-alert');
		$route->post('create-reminder/<id>', 'create-reminder');
		$route->post('create-notification/<id>', 'create-notification');
		$route->post('view-message/<id>', 'view-message');

		$route->prefix('<id>', static function (RouteInterface $route) {
			$route->put()->action('update');
			$route->delete()->action('delete');
		});
	});

	$router->controller('media')->group(static function (RouteInterface $route) {
		$route->get()->action('index');
		$route->get('<id>', 'view');
		$route->delete('<id>', 'delete');
	});

	$router->controller('survey')->group(static function (RouteInterface $route) {
		$route->get()->action('index');

		$route->post()->action('create');
		$route->post('with-survey-question-answer', 'create-with-survey-question-answer');

		$route->prefix('<id>', static function (RouteInterface $route) {
			$route->get()->action('view');
			$route->get('with-questions', 'view-with-questions');

			$route->put()->action('update');
			$route->put('with-survey-question-answer', 'update-with-survey-question-answer');
			$route->delete()->action('delete');
		});
	});

	$router->controller('question')->group(static function (RouteInterface $route) {
		$route->get()->action('index');
		$route->get('with-question-answer', 'index-with-question-answer');

		$route->post('/', 'create');
		$route->post('with-question-answer', 'create-with-question-answer');

		$route->prefix('<id>', static function (RouteInterface $route) {
			$route->get()->action('view');
			$route->put()->action('update');
			$route->delete()->action('delete');
		});
	});

	$router->controller('equipment')->group(static function (RouteInterface $route) {
		$route->crud();
		$route->post('<id>/called')->action('called');
	});

	$router->controller('question-answer')->group(static function (RouteInterface $route) {
		$route->get('with-questions');
		$route->crud();
	});

	$router->controller('task')->group(static function (RouteInterface $route) {
		$route->get()->action('index');
		$route->get('counts');
		$route->get('relations');
		$route->get('statistic');

		$route->post()->action('create');
		$route->post('for-users', 'create-for-users');
		$route->post('change-status/<id>', 'change-status');

		$route->prefix('<id>', static function (RouteInterface $route) {
			$route->get()->action('view');
			$route->put()->action('update');
			$route->delete()->action('delete');

			$route->get('history');

			$route->post('read');
			$route->post('assign');
			$route->post('postpone');
			$route->post('restore');

			$route->prefix('files', static function (RouteInterface $route) {
				$route->get()->action('files');
				$route->post()->action('create-files');
				$route->delete()->action('delete-files');
			});

			$route->prefix('comments', static function (RouteInterface $route) {
				$route->get()->action('comments');
				$route->post()->action('create-comments');
			});
		});
	});

	$router->controller('task-tag')->group(static function (RouteInterface $route) {
		$route->get('all');
		$route->crud();
	});

	$router->controller('task-comment')->crud(['index', 'view', 'update', 'delete']);

	$router->controller('task-favorite')->group(static function (RouteInterface $route) {
		$route->crud(['index', 'create', 'delete']);
		$route->post('<id>/change-position', 'change-position');
	});

	$router->controller('utilities')->disablePluralize()->group(static function (RouteInterface $route) {
		$route->post('fix-land-object-purposes');
		$route->post('reassign-consultants-to-companies');
	});

	$router->controller('folder')->group(static function (RouteInterface $route) {
		$route->get()->action('index');
		$route->get('entities');

		$route->post()->action('create');
		$route->post('reorder');

		$route->prefix('<id>', static function (RouteInterface $route) {
			$route->put()->action('update');
			$route->delete()->action('delete');

			$route->post('entities')->action('add-entities');
			$route->delete('entities')->action('remove-entities');
		});
	});
};