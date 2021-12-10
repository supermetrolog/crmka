<?php

namespace app\daemons;

use app\models\CallList;
use app\models\Notification;
use consik\yii2websocket\events\WSClientMessageEvent;
use consik\yii2websocket\WebSocketServer;
use Prophecy\Call\Call;
use Ratchet\ConnectionInterface;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;

class ServerWS extends WebSocketServer
{

    private const MESSAGE_TEMPLATE = ['message' => '', 'action' => 'info', 'error' => false, 'success' => false];
    protected function getCommand(ConnectionInterface $from, $msg)
    {
        $request = json_decode($msg, true);
        return !empty($request['action']) ? $request['action'] : parent::getCommand($from, $msg);
    }

    /**
     * Implement command's method using "command" as prefix for method name
     *
     * method for user's command "ping"
     */

    function commandPing(ConnectionInterface $client, $msg)
    {
        $client->send('Pong');
    }
    function commandCheckCall(ConnectionInterface $client, $msg)
    {
        echo "\ncommandCheckCall!\n";
        $result = self::MESSAGE_TEMPLATE;

        $modelsForUpdate = CallList::find()->joinWith(['caller'])->where(['user_profile.user_id' => $client->name])->andWhere(['call_list.status' => null])->all();
        $models = CallList::find()->joinWith(['caller', 'phoneFrom' => function ($query) {
            $query->with(['contact']);
        }, 'phoneTo' => function ($query) {
            $query->with(['contact']);
        }])->where(['user_profile.user_id' => $client->name])->andWhere(['call_list.status' => null])->asArray()->all();

        foreach ($modelsForUpdate as $model) {
            $model->changeViewed(CallList::VIEWED_REQUESTED);
        }
        // $data = ArrayHelper::toArray($models);
        $result['action'] = 'current_calls';
        $result['message'] = $models;
        $client->send(json_encode($result));
    }
    function commandCheckNewNotifications(ConnectionInterface $client, $msg)
    {
        echo "\ncommandCheckNewNotification!\n";
        $result = self::MESSAGE_TEMPLATE;

        $models = Notification::find()->where(['notification.consultant_id' => $client->name])->andWhere(['status' => Notification::NO_FETCHED_STATUS])->all();
        $response = Notification::array_copy($models);
        Notification::changeNoFetchedStatusToFetched($models);
        $models = $response;

        $result['action'] = 'new_notifications';
        $result['message'] = $models;
        $client->send(json_encode($result));
    }
    function commandEcho(ConnectionInterface $client, $msg)
    {
        echo "CommandEcho!";
        $client->send($msg);
    }
    function commandGetName(ConnectionInterface $client, $msg)
    {
        echo "CommandGetName!";
        $result = self::MESSAGE_TEMPLATE;
        $result['message'] = $client->name;
        $client->send(json_encode($result));
    }
    function commandSetUserID(ConnectionInterface $client, $msg)
    {
        echo "SetUserID!";
        $msg = json_decode($msg);
        $result = self::MESSAGE_TEMPLATE;
        if ($client->name) {
            $result['message'] = "Ваш UserID [{$client->name}] уже зарегистрирован в сокете!";
            $result['error'] = true;
            return $client->send(json_encode($result));
        }
        $client->name = $msg->data->user_id;
        $result['action'] = 'user_id_seted';
        $result['message'] = "Ваш UserID [{$client->name}] зарегистрирован!";
        $result['success'] = true;
        return $client->send(json_encode($result));
    }
}
