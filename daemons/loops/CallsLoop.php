<?php

namespace app\daemons\loops;

use app\daemons\loops\BaseLoop;
use app\daemons\Message;
use app\models\CallList;
use app\models\Notification;
use yii\base\Model;
use yii\helpers\ArrayHelper;

class CallsLoop extends BaseLoop
{
    private $lastSendedCurrentCalls = [];
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
            $message->setBody(count($modelsArray[$user_id]));
            $this->clients->sendClientPool($user_id, $message);
        }
        CallList::changeNoFetchedStatusToFetched($models);
    }

    private function getCurrentCalls()
    {
        $models = CallList::find()->with(['caller', 'phoneFrom.contact.company', 'phoneTo.contact.company'])->andWhere(['is', 'call_ended_status', new \yii\db\Expression('null')])->all();
        $models = array_map(function ($model) {
            return $model->toArray([], ['caller', 'phoneFrom.contact.company', 'phoneTo.contact.company']);
        }, $models);
        $message = new Message();
        $message->setAction('update_current_calls');
        $message->setBody($models);
        if (!$this->modelsEqual($models, $this->lastSendedCurrentCalls)) {
            $this->clients->sendAllClients($message);
        }
        $this->lastSendedCurrentCalls = $models;
    }

    private function modelsEqual($models1, $models2)
    {
        $ids1 = [];
        foreach ($models1 as $model) {
            $ids1[] = $model->id;
        }

        $ids2 = [];
        foreach ($models2 as $model) {
            $ids2[] = $model->id;
        }
        return $ids1 == $ids2;
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
