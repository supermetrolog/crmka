<?php

namespace app\daemons;

use app\components\ConsoleLogger;
use app\daemons\loops\CallsLoop;
use app\models\CallList;
use app\models\Notification;
use consik\yii2websocket\WebSocketServer;
use Ratchet\ConnectionInterface;
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use consik\yii2websocket\events\ExceptionEvent;
use app\daemons\loops\NotifyLoop;
use Yii;

class ServerWS extends WebSocketServer
{

    public const MESSAGE_TEMPLATE = ['message' => '', 'action' => 'info', 'error' => false, 'success' => false];
    private $_clients;
    private $timeout = 2;
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
            $this->trigger(self::EVENT_WEBSOCKET_OPEN);
            $this->clients = new \SplObjectStorage();
            $this->initServerData();
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
    public function initServerData()
    {
        $this->_clients = new Clients();


        $loop = new NotifyLoop;
        $this->server->loop->addPeriodicTimer($this->timeout, function () use ($loop) {
            try {
                $loop->run($this->_clients);
            } catch (yii\db\Exception $e) {
                Yii::$app->db->close();
                Yii::$app->db->open();
                ConsoleLogger::info($e->getMessage());
                $loop->run($this->_clients);
            }
        });

        $loop = new CallsLoop;
        $this->server->loop->addPeriodicTimer($this->timeout, function () use ($loop) {
            try {
                $loop->run($this->_clients);
            } catch (yii\db\Exception $e) {
                Yii::$app->db->close();
                Yii::$app->db->open();
                ConsoleLogger::info($e->getMessage());
                $loop->run($this->_clients);
            }
        });


        $this->on(WebSocketServer::EVENT_CLIENT_DISCONNECTED, function ($e) {
            ConsoleLogger::info("client disconneted");
            $this->_clients->removeClient($e->client);
        });
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
        $message = new Message();
        $message->setAction('pong');
        $message->setBody("pong");
        return $this->_clients->sendClient($client, $message);
    }
    function commandEcho(ConnectionInterface $client, $msg)
    {
        ConsoleLogger::info('command echo');
        $client->send($msg);
    }

    function commandSendPool(ConnectionInterface $client, $msg)
    {
        ConsoleLogger::info('send pool');
        $msg = json_decode($msg);
        $message = new Message();
        $message->setBody($msg->data->message);
        $message->setAction($msg->data->action);
        return $this->_clients->sendClientPool($client->name->user_id, $message);
    }
    function commandSetUser(ConnectionInterface $client, $msg)
    {
        try {
            ConsoleLogger::info('set user');
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
        } catch (\Throwable $th) {
            ConsoleLogger::info($th->getMessage());
            throw $th;
        }
    }
}
