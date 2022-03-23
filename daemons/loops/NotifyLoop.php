<?php

namespace app\daemons\loops;

use app\daemons\loops\BaseLoop;
use app\daemons\Message;
use app\models\Notification;
use yii\helpers\ArrayHelper;

class NotifyLoop extends BaseLoop
{
    public function processed()
    {
        $users_ids = $this->clients->getClientsIds();
        $models = Notification::find()->where(['notification.consultant_id' => $users_ids])->andWhere(['status' => Notification::NO_FETCHED_STATUS])->all();
        $modelsArray = $this->changeIndex($models, 'consultant_id');
        $message = new Message();
        $message->setAction(Message::ACTION_NEW_NOTIFICATION);
        foreach ($modelsArray as $user_id => $userNotify) {
            $message->setBody(count($modelsArray));
            $this->clients->sendClientPool($user_id, $message);
        }
        Notification::changeNoFetchedStatusToFetched($models);
    }
}
