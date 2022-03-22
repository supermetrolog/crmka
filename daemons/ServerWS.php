<?php

namespace app\daemons;

use app\models\CallList;
use app\models\Notification;
use consik\yii2websocket\WebSocketServer;
use Ratchet\ConnectionInterface;
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use consik\yii2websocket\events\ExceptionEvent;
use app\daemons\loops\NotifyLoop;

class ServerWS extends WebSocketServer
{

    public const MESSAGE_TEMPLATE = ['message' => '', 'action' => 'info', 'error' => false, 'success' => false];
    private $_clients;
    private $timeout = 1;
    public function start()
    {
        try {
            $this->server = IoServer::factory(
                new HttpServer(
                    new WsServer(
                        $this
                    )
                ),
                $this->port
            );
            $this->_clients = new Clients();
            $this->trigger(self::EVENT_WEBSOCKET_OPEN);
            $this->clients = new \SplObjectStorage();
            $notifyLoop = new NotifyLoop;
            $this->server->loop->addPeriodicTimer($this->timeout, function () use ($notifyLoop) {
                echo "Timer!\n";
                $notifyLoop->run($this->_clients);
            });

            $this->on(WebSocketServer::EVENT_CLIENT_DISCONNECTED, function ($e) {
                echo "\nCLIENT DISCONNECTED\n";
                $this->_clients->removeClient($e->client);
            });

            $this->server->run();

            return true;
        } catch (\Exception $e) {
            $errorEvent = new ExceptionEvent([
                'exception' => $e
            ]);
            $this->trigger(self::EVENT_WEBSOCKET_OPEN_ERROR, $errorEvent);
            return false;
        }
    }
    public static function sendAllClients(array $clients, Message $msg)
    {
        foreach ($clients as $client_pool) {
            foreach ($client_pool as  $client) {
                $client->send($msg->getData());
            }
        }
    }
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
    // function commandCheckCall(ConnectionInterface $client, $msg)
    // {
    //     echo "\ncommandCheckCall!\n";
    //     $result = self::MESSAGE_TEMPLATE;

    //     $modelsForUpdate = CallList::find()->joinWith(['caller'])->where(['user_profile.user_id' => $client->name])->andWhere(['call_list.status' => null])->all();
    //     $models = CallList::find()->joinWith(['caller', 'phoneFrom' => function ($query) {
    //         $query->with(['contact']);
    //     }, 'phoneTo' => function ($query) {
    //         $query->with(['contact']);
    //     }])->where(['user_profile.user_id' => $client->name])->andWhere(['call_list.status' => null])->asArray()->all();

    //     foreach ($modelsForUpdate as $model) {
    //         $model->changeViewed(CallList::VIEWED_REQUESTED);
    //     }
    //     // $data = ArrayHelper::toArray($models);
    //     $result['action'] = 'current_calls';
    //     $result['message'] = $models;
    //     $client->send(json_encode($result));
    // }
    // function commandEcho(ConnectionInterface $client, $msg)
    // {
    //     echo "CommandEcho!";
    //     $client->send($msg);
    // }
    // function commandGetName(ConnectionInterface $client, $msg)
    // {
    //     echo "CommandGetName!";
    //     $result = self::MESSAGE_TEMPLATE;
    //     $result['message'] = $client->name;
    //     $client->send(json_encode($result));
    // }

    function commandSendPool(ConnectionInterface $client, $msg)
    {
        echo "SendPool!\n";
        $msg = json_decode($msg);
        $message = new Message();
        $message->setBody($msg->data->message);
        $message->setAction($msg->data->action);
        return $this->_clients->sendClientPool($client->name->user_id, $message);
    }
    // function commandSetUserID(ConnectionInterface $client, $msg)
    // {
    //     echo "SetUserID!";
    //     $msg = json_decode($msg);
    //     $result = self::MESSAGE_TEMPLATE;
    //     if ($client->name) {
    //         $result['message'] = "Ваш UserID [{$client->name}] уже зарегистрирован в сокете!";
    //         $result['error'] = true;
    //         return $client->send(json_encode($result));
    //     }
    //     $client->name = $msg->data;
    //     $this->_clients[$client->name][] = $client;
    //     $result['action'] = 'user_id_seted';
    //     $result['message'] = "Yout UserID [{$client->name}] was seted!";
    //     $result['success'] = true;
    //     return $client->send(json_encode($result));
    // }
    function commandSetUser(ConnectionInterface $client, $msg)
    {
        echo "SetUser!";
        $msg = json_decode($msg);
        $message = new Message();
        $message->setAction('user_setted');
        if ($this->_clients->clientExist($client)) {
            $message->setError();
            $message->setBody("You already registered in websocket! ({$client->name->user_id})");
            return $this->_clients->sendClient($client, $message);
        }
        $client->name = $msg->data;
        $this->_clients->setClient($client);
        $message->setBody("You successfuly registered ({$client->name->user_id})");
        return $this->_clients->sendClient($client, $message);
    }
}
