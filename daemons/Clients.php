<?php

namespace app\daemons;

use app\components\ConsoleLogger;
use Ratchet\ConnectionInterface;
use yii\base\Model;
use yii\helpers\ArrayHelper;

class Clients extends Model
{
    private $clients_pool = [];

    public function setClient(ConnectionInterface $client)
    {
        $this->clients_pool[$client->name->user_id][] = $client;
        ConsoleLogger::info("setted user with ID: " . $client->name->user_id);
    }

    public function removeClient(ConnectionInterface $client)
    {
        if (!$this->clientExist($client)) {
            ConsoleLogger::info('do not removed client because him not exist!');
            return false;
        }
        $pool = $this->clients_pool[$client->name->user_id];
        foreach ($pool as $key => $_client) {
            if ($_client->name->window_id == $client->name->window_id) {
                unset($pool[$key]);
            }
        }

        $this->clients_pool[$client->name->user_id] = $pool;
        if (!count($this->clients_pool[$client->name->user_id])) {
            unset($this->clients_pool[$client->name->user_id]);
        }
        ConsoleLogger::info('removed user');
        return true;
    }

    public function sendClient(ConnectionInterface $client, Message $msg)
    {
        return $client->send($msg->getData());
    }
    public function sendClientPool(int $user_id, Message $msg, ConnectionInterface $notAsweredclient = null)
    {
        if (!ArrayHelper::keyExists($user_id, $this->clients_pool)) {
            ConsoleLogger::info('not exist pool');
            return false;
        }
        ConsoleLogger::info('send client pool');
        $pool = $this->clients_pool[$user_id];
        foreach ($pool as $_client) {
            if ($notAsweredclient) {
                if ($_client->name->window_id == $notAsweredclient->name->window_id) {
                    continue;
                }
                $this->sendClient($_client, $msg);
            } else {
                $this->sendClient($_client, $msg);
            }
        }
        return true;
    }

    public function sendAllClients(Message $msg, ConnectionInterface $notAsweredclient = null)
    {
        ConsoleLogger::info('send all clients');
        foreach ($this->clients_pool as $user_id => $pool) {
            $this->sendClientPool($user_id, $msg);
        }
        ConsoleLogger::info('sended all clients');
    }
    public function clientExist(ConnectionInterface $client)
    {
        if (!$client->name) {
            return false;
        }

        if (!ArrayHelper::keyExists($client->name->user_id, $this->clients_pool)) {
            return false;
        }

        $pool = $this->clients_pool[$client->name->user_id];
        foreach ($pool as $_client) {
            if ($_client->name->window_id == $client->name->window_id) {
                return true;
            }
        }

        return false;
    }
    public function isExistByUserID(int $id): bool
    {
        return ArrayHelper::keyExists($id, $this->clients_pool);
    }
    public function getClientsIds()
    {
        $ids = [];
        foreach ($this->clients_pool as $key => $value) {
            $ids[] = $key;
        }

        return $ids;
    }
}
