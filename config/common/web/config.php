<?php

use app\components\Media\Media;
use app\components\PathBuilder\PathBuilderFactory;
use yii\di\Container;
use yii\helpers\ArrayHelper;

return ArrayHelper::merge(
	require __DIR__ . '/../common/config.php',
	[
		'id'         => 'basic',
		'components' => [
			'request'      => [
				'enableCsrfValidation' => false,
				'cookieValidationKey'  => 'p6xr64xCH9KxL1zQ7zgdZ6BzV6IH2yZl',
				'parsers'              => [
					'application/json'    => 'yii\web\JsonParser',
					'multipart/form-data' => 'yii\web\MultipartFormDataParser'
				],
				'baseUrl'              => ''
			],
			'user'         => [
				'identityClass'   => 'app\models\User',
				'enableAutoLogin' => true,
				'enableSession'   => false,
			],
			'errorHandler' => [
				'errorAction' => 'site/error',
			],
			'urlManager'   => [
				'enablePrettyUrl'     => true,
				'enableStrictParsing' => true,
				'showScriptName'      => false,
				'rules'               => require __DIR__ . "/url_rules.php"
			]
		],
		'container'  => [
			'singletons' => [
				Media::class => function (Container $container) {
					return new Media(
						$container->get(PathBuilderFactory::class),
						Yii::$app->request->hostInfo . Yii::$app->params['media']['baseFolder'],
						Yii::$app->params['media']['diskPath']
					);
				}
			]
		]
	]
);
