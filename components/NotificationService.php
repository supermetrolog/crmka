<?php

namespace app\components;

use app\events\NotificationEvent;
use app\exceptions\ValidationErrorHttpException;
use app\models\Notification;
use Yii;
use yii\base\Component;

class NotificationService extends Component
{
	public function notifyUser(NotificationEvent $event)
	{
		$model = new Notification();

		$model->consultant_id = $event->consultant_id;
		$model->type          = $event->type;
		$model->title         = $event->title;
		$model->body          = $event->body;
		$model->status        = Notification::NO_FETCHED_STATUS;

		if (!$model->save()) {
			throw new ValidationErrorHttpException($model->getErrorSummary(false));
		}

		$notifyQueue = Yii::$app->notifyQueue;

		$notifyQueue->publish($model->getAttributes());

		return true;
	}
}
