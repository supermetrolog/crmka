<?php

declare(strict_types=1);

use app\components\EventManager;
use app\components\MessageTemplate\Adapters\EmailTwigEnvironmentAdapter;
use app\components\MessageTemplate\Interfaces\EmailTwigEnvironmentInterface;
use app\components\Notification\Interfaces\WebsocketPublisherInterface;
use app\components\Notification\RabbitMqWebsocketPublisher;
use app\components\Telegram\Interfaces\TelegramDeepLinkGeneratorInterface;
use app\components\Telegram\TelegramBotApiClient;
use app\components\Telegram\TelegramDeepLinkGenerator;
use app\components\Whatsapp\WhatsappApiClient;
use app\kernel\common\database\interfaces\transaction\TransactionBeginnerInterface;
use app\models\ActiveQuery\NotificationChannelQuery;
use app\models\Notification\NotificationChannel;
use app\usecases\Auth\AuthService;
use Twig\Loader\FilesystemLoader;

$db     = require __DIR__ . "/db.php";
$old_db = require __DIR__ . "/db_old.php";

$secrets = require YII_PROJECT_ROOT . "/config/secrets.php";
$params  = require YII_PROJECT_ROOT . '/config/common/common/params.php';

return [
	'singletons'  => [
		'db'                                 => $db,
		'old_db'                             => $old_db,
		TransactionBeginnerInterface::class  => 'db',
		EventManager::class                  => [
			'class'  => EventManager::class,
			'config' => require YII_PROJECT_ROOT . '/config/common/common/events.php'
		],
		EmailTwigEnvironmentInterface::class => static function () {
			$loader = new FilesystemLoader(Yii::getAlias('@app/components/MessageTemplate/Twig'));

			return new EmailTwigEnvironmentAdapter($loader, [
				'cache' => Yii::getAlias('@runtime/Twig/email-cache'),
				'debug' => YII_DEBUG,
			]);
		},
		WebsocketPublisherInterface::class   => static fn() => new RabbitMqWebsocketPublisher(Yii::$app->notifyQueue),
	],
	'definitions' => [
		NotificationChannelQuery::class           => [
			'class'      => NotificationChannelQuery::class,
			'modelClass' => NotificationChannel::class
		],
		AuthService::class              => [
			'class'            => AuthService::class,
			'allowedOfficeIps' => $params['allowed_office_ips']
		],
		TelegramBotApiClient::class               => static fn() => new TelegramBotApiClient(Yii::$app->params['crm_telegram_bot']['apiUrl']),
		TelegramDeepLinkGeneratorInterface::class => [
			'class'   => TelegramDeepLinkGenerator::class,
			'botName' => $params['crm_telegram_bot']['name'],
			'webBase' => $params['crm_telegram_bot']['deepLink']['webBase'],
			'appBase' => $params['crm_telegram_bot']['deepLink']['appBase'],
			'param'   => $params['crm_telegram_bot']['deepLink']['param'],
			'prefer'  => $params['crm_telegram_bot']['deepLink']['prefer'],
		]
	]
];
