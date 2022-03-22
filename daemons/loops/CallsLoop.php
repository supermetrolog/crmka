<?php

namespace app\daemons\loops;

use app\daemons\loops\BaseLoop;
use app\daemons\Message;
use app\models\CallList;
use app\models\Notification;

class CallsLoop extends BaseLoop
{
    public function processed()
    {
        $users_ids = $this->clients->getClientsIds();
        $models = CallList::find()->joinWith(['caller'])->where(['user_profile.user_id' => $users_ids])->andWhere(['status' => Notification::NO_FETCHED_STATUS])->all();
        $modelsArray = $this->changeIndex($models);
        $message = new Message();
        $message->setAction(Message::ACTION_NEW_CALL);
        foreach ($modelsArray as $user_id => $userCall) {
            $message->setBody($userCall);
            $this->clients->sendClientPool($user_id, $message);
        }
        CallList::changeNoFetchedStatusToFetched($models);
    }

    protected function changeIndex($array, $index = null)
    {
        $newArray = [];
        foreach ($array as $key => $value) {
            $newArray[$value['caller']['user_id']][] = $value;
        }
        return $newArray;
    }
}
