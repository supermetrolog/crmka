<?php

declare(strict_types=1);

namespace app\commands;

use app\components\Notification\Factories\NotifierFactory;
use app\components\Notification\TestNotification;
use app\models\Notification\NotificationChannel;
use yii\console\Controller;

class TestController extends Controller
{
	private NotifierFactory $notificationBuilderFactory;

	public function __construct($id, $module, NotifierFactory $notificationBuilderFactory, array $config = [])
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
			->setNotification(new TestNotification())
			->setNotifiable(new TestNotification())
			->setSendNow(true)
			->send();
	}
}