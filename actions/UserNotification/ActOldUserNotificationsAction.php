<?php

declare(strict_types=1);

namespace app\actions\UserNotification;

use app\kernel\common\actions\Action;
use app\models\Notification\UserNotification;
use yii\base\ErrorException;

class ActOldUserNotificationsAction extends Action
{
	private string $createdBeforeDate = '2025-09-10 00:00:00';

	/**
	 * @throws ErrorException
	 */
	public function run(): void
	{
		$query = UserNotification::find()->andWhere(['<=', 'created_at', $this->createdBeforeDate])->notActed();

		$count = 0;

		/** @var UserNotification $notification */
		foreach ($query->each() as $notification) {
			$notification->updateAttributes(['acted_at' => $this->createdBeforeDate]);

			$count++;
		}

		$this->infof('Acted %d old notifications', $count);
	}
}