<?php

namespace app\daemons\loops;

use app\daemons\loops\BaseLoop;
use app\daemons\Message;
use app\models\CallList;
use app\models\Notification;
use yii\helpers\ArrayHelper;

class CallsLoop extends BaseLoop
{
    private $sendedLastEmptyData = false;
    public function processed()
    {
        $this->getUserCalls();
        $this->getCurrentCalls();
    }

    private function getUserCalls()
    {
        $users_ids = $this->clients->getClientsIds();
        $models = CallList::find()->joinWith(['caller'])->where(['user_profile.user_id' => $users_ids])->andWhere(['status' => Notification::NO_FETCHED_STATUS])->all();
        $modelsArray = $this->changeIndex($models);
        $message = new Message();
        $message->setAction(Message::ACTION_NEW_CALL);
        foreach ($modelsArray as $user_id => $userCall) {
            $message->setBody(count($modelsArray));
            $this->clients->sendClientPool($user_id, $message);
        }
        CallList::changeNoFetchedStatusToFetched($models);
    }

    private function getCurrentCalls()
    {
        $models = CallList::find()->with(['caller'])->andWhere(['is', 'call_ended_status', new \yii\db\Expression('null')])->asArray()->all();
        $message = new Message();
        $message->setAction('update_current_calls');
        $message->setBody($models);

        if (count($models)) {
            $this->clients->sendAllClients($message);
            $this->sendedLastEmptyData = false;
        } else {
            if (!$this->sendedLastEmptyData) {
                $this->clients->sendAllClients($message);
                $this->sendedLastEmptyData = true;
            }
        }
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
