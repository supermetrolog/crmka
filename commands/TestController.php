<?php

declare(strict_types=1);

namespace app\commands;

use app\components\Notification\Factories\NotifierFactory;
use app\components\Notification\Notification;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\Notification\NotificationChannel;
use app\models\User;
use Throwable;
use yii\base\ErrorException;
use yii\console\Controller;

class TestController extends Controller
{
	private NotifierFactory $notifierFactory;

	public function __construct($id, $module, NotifierFactory $notifierFactory, array $config = [])
	{
		$this->notifierFactory = $notifierFactory;

		parent::__construct($id, $module, $config);
	}

	/**
	 * @throws SaveModelException
	 * @throws ErrorException
	 * @throws Throwable
	 */
	public function actionIndex(): void
	{
		$model = $this->notifierFactory
			->create()
			->setChannel(NotificationChannel::WEB)
			->setNotification(new Notification('Subject', 'Message'))
			->setNotifiable(User::find()->one())
			->setCreatedByType(User::getMorphClass())
			->setCreatedById(User::find()->one()->id)
			->send();
	}
}