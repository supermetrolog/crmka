<?php

declare(strict_types=1);

namespace app\commands;

use app\components\Notification\Factories\NotificationBuilderFactory;
use app\components\Notification\TestNotification;
use app\models\Notification\NotificationChannel;
use yii\console\Controller;

class TestController extends Controller
{
	private NotificationBuilderFactory $notificationBuilderFactory;

	public function __construct($id, $module, NotificationBuilderFactory $notificationBuilderFactory, array $config = [])
	{
		$this->notificationBuilderFactory = $notificationBuilderFactory;

		parent::__construct($id, $module, $config);
	}

	public function actionIndex(): void
	{
		$model = $this->notificationBuilderFactory
			->create()
			->addChannel(NotificationChannel::WEB)
			->addChannel(NotificationChannel::EMAIL)
			->withNotifiable(new TestNotification())
			->withNotification(new TestNotification())
			->sendNow()
			->build()
			->send();
	}
}