<?php

return
	[
		[
			'class'         => 'yii\rest\UrlRule',
			'controller'    => 'user',
			'except'        => [],
			'extraPatterns' => [
				'POST,OPTIONS login'  => 'login',
				'POST,OPTIONS logout' => 'logout',
			],
		],
		[
			'class'         => 'yii\rest\UrlRule',
			'controller'    => 'company',
			'except'        => [],
			'extraPatterns' => [
				'GET,OPTIONS search'             => 'search',
				'GET,OPTIONS product-range-list' => 'product-range-list',
				'GET,OPTIONS in-the-bank-list'   => 'in-the-bank-list',
			],
		],
		[
			'class'      => 'yii\rest\UrlRule',
			'controller' => 'companygroup',
			'except'     => [],
		],
		[
			'class'      => 'yii\rest\UrlRule',
			'controller' => 'calendar',
			'except'     => [],
		],
		[
			'class'         => 'yii\rest\UrlRule',
			'controller'    => 'request',
			'except'        => [],
			'extraPatterns' => [
				'GET,OPTIONS company-requests/<id>' => 'company-requests',
				'GET,OPTIONS search'                => 'search',
				'PATCH,OPTIONS disable/<id>'        => 'disable',
				'PATCH,OPTIONS undisable/<id>'      => 'undisable',
			],
		],
		[
			'class'         => 'yii\rest\UrlRule',
			'controller'    => 'contact',
			'except'        => [],
			'extraPatterns' => [
				'GET,OPTIONS company-contacts/<id>' => 'company-contacts',
				'POST create-comment'               => 'create-comment',
				'OPTIONS create-comment'            => 'options',
			],
		],
		[
			'class'         => 'yii\rest\UrlRule',
			'controller'    => ['timeline' => 'timeline'],
			'except'        => [],
			'extraPatterns' => [
				'GET /'                            => 'index',
				'PATCH update-step/<id>'           => 'update-step',
				'OPTIONS update-step/<id>'         => 'options',
				'POST,OPTIONS add-objects/<id>'    => 'add-objects',
				'GET,OPTIONS search'               => 'search',
				'GET,OPTIONS action-comments/<id>' => 'action-comments',
				'POST,OPTIONS send-objects'        => 'send-objects',
				'POST,OPTIONS add-action-comments' => 'add-action-comments',
			],
		],
		[
			'class'         => 'yii\rest\UrlRule',
			'controller'    => 'notification',
			'except'        => [],
			'extraPatterns' => [
				'GET,OPTIONS <id>/viewed-not-count' => 'viewed-not-count',
				'GET,OPTIONS <id>/viewed-all'       => 'viewed-all',
				'GET,OPTIONS <id>/count'            => 'count',
			],
		],
		[
			'class'         => 'yii\rest\UrlRule',
			'controller'    => 'calllist',
			'except'        => [],
			'extraPatterns' => [
				'GET,OPTIONS <caller_id>/viewed-not-count' => 'viewed-not-count',
				'GET,OPTIONS <caller_id>/viewed-all'       => 'viewed-all',
				'GET,OPTIONS <caller_id>/count'            => 'count',
			],
		],
		[
			'class'         => 'yii\rest\UrlRule',
			'controller'    => 'pdf',
			'except'        => [],
			'extraPatterns' => [
				'GET fuck' => 'fuck',
			],
		],
		[
			'class'      => 'yii\rest\UrlRule',
			'controller' => 'site',
			'except'     => [],
		],
		[
			'class'         => 'yii\rest\UrlRule',
			'controller'    => 'oldDb/object',
			'except'        => [],
			'extraPatterns' => [
				'GET,OPTIONS offers'                        => 'offers',
				'GET,OPTIONS offers-map'                    => 'offers-map',
				'GET,OPTIONS offers-count'                  => 'offers-count',
				'GET,OPTIONS offers-map-count'              => 'offers-map-count',
				'POST,OPTIONS toggle-avito-ad/<originalId>' => 'toggle-avito-ad',
				'POST,OPTIONS toggle-is-fake/<originalId>'  => 'toggle-is-fake',
			],
		],
		[
			'class'         => 'yii\rest\UrlRule',
			'controller'    => 'oldDb/location',
			'except'        => [],
			'extraPatterns' => [
				'GET,OPTIONS region-list' => 'region-list',
			],
		],
		[
			'class'         => 'yii\rest\UrlRule',
			'controller'    => 'pdf/presentation',
			'extraPatterns' => [
				'GET html' => 'html',
			],
		],
		[
			'class'      => 'yii\rest\UrlRule',
			'controller' => 'deal',
			'except'     => [],
		],
		[
			'class'      => 'yii\rest\UrlRule',
			'controller' => 'favorite-offer',
			'except'     => [],
		],
		[
			'class'         => 'yii\rest\UrlRule',
			'controller'    => 'letter',
			'except'        => [],
			'extraPatterns' => [
				'POST,OPTIONS send' => 'send'
			]
		],
		[
			'class'         => 'yii\rest\UrlRule',
			'controller'    => ['company-events-log' => 'company-events-log'],
			'except'        => [],
			'extraPatterns' => [],
		],
		[
			'class'         => 'yii\rest\UrlRule',
			'controller'    => ['objects' => 'objects'],
			'extraPatterns' => [
				'GET,OPTIONS' => 'index'
			],
		],
		[
			'class'         => 'yii\rest\UrlRule',
			'controller'    => ['complex' => 'complex'],
			'extraPatterns' => [
				'GET,OPTIONS <id>' => 'view'
			],
		],
		[
			'class'         => 'yii\rest\UrlRule',
			'controller'    => ['archiver' => 'archiver'],
			'extraPatterns' => [
				'GET,OPTIONS download' => 'download'
			],
		],
		[
			'class'         => 'yii\rest\UrlRule',
			'controller'    => 'task',
			'except'        => [],
			'extraPatterns' => [
				'GET,OPTIONS'                     => 'index',
				'GET,OPTIONS <id>'                => 'index',
				'POST,OPTIONS'                    => 'create',
				'POST,OPTIONS for-users'          => 'create-for-users',
				'PUT,OPTIONS <id>'                => 'update',
				'DELETE,OPTIONS <id>'             => 'delete',
				'POST,OPTIONS change-status/<id>' => 'change-status',
			],
		],
		[
			'class'         => 'yii\rest\UrlRule',
			'controller'    => ['chat-members' => 'ChatMember/chat-member'],
			'extraPatterns' => [
				'GET,OPTIONS'                     => 'index',
				'GET,OPTIONS <id>/pinned-message' => 'pinned-message',
				'POST,OPTIONS pin-message'        => 'pin-message',
				'POST,OPTIONS unpin-message'      => 'unpin-message',
				'GET,OPTIONS <id>'                => 'view',
			],
		],
		[
			'class'         => 'yii\rest\UrlRule',
			'controller'    => ['chat-member-messages' => 'ChatMember/chat-member-message'],
			'extraPatterns' => [
				'POST,OPTIONS'                  => 'create',
				'PUT,OPTIONS <id>'              => 'update',
				'DELETE,OPTIONS <id>'           => 'delete',
				'POST,OPTIONS create-task/<id>' => 'create-task',
			],
		],
		[
			'class'         => 'yii\rest\UrlRule',
			'controller'    => ['chat-member-message-tags' => 'ChatMember/chat-member-message-tag'],
			'extraPatterns' => [
				'GET,OPTIONS'         => 'index',
				'GET,OPTIONS <id>'    => 'view',
				'POST,OPTIONS'        => 'create',
				'PUT,OPTIONS <id>'    => 'update',
				'DELETE,OPTIONS <id>' => 'delete',
			],
		],
	];
