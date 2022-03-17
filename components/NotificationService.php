<?php

namespace app\components;

use app\exceptions\ValidationErrorHttpException;
use app\models\Notification;
use yii\base\Component;

class NotificationService  extends Component
{
    public function notifyUser($event)
    {
        $model = new Notification();

        $model->consultant_id = $event->consultant_id;
        $model->type = $event->type;
        $model->title = $event->title;
        $model->body = $event->body;
        $model->status = Notification::NO_FETCHED_STATUS;
        if ($model->save()) {
            return true;
        }

        throw new ValidationErrorHttpException($model->getErrorSummary(false));
    }
}
