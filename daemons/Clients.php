<?php

namespace app\daemons;

use Exception;
use Ratchet\ConnectionInterface;
use yii\base\InvalidValueException;
use yii\base\Model;
use yii\helpers\ArrayHelper;

class Clients extends Model
{
    private $clients_pool = [];

    public function setClient(ConnectionInterface $client)
    {
        $this->clients_pool[$client->name->user_id] = $client;
        echo "Setted User\n";
        var_dump($this->clients_pool);
    }

    public function removeClient(ConnectionInterface $client)
    {
        if (!ArrayHelper::keyExists($client->name->user_id, $this->clients_pool)) {
            new Exception('Do not removed client because him not exist!');
        }
        $pool = $this->clients_pool[$client->name->user_id];
        foreach ($pool as $key => $_client) {
            if ($_client->name->window_id == $client->name->window_id) {
                unset($pool[$key]);
            }
        }

        $this->clients_pool[$client->name->user_id] = $pool;
        echo "Removed User\n";
        var_dump($this->clients_pool);
    }

    public function sendClient(ConnectionInterface $client, Message $msg)
    {
        return $client->send($msg->getData());
    }
    public function sendClientPool(int $user_id, Message $msg, ConnectionInterface $notAsweredclient = null)
    {
        if (!ArrayHelper::keyExists($user_id, $this->clients_pool)) {
            new Exception('Not exist pool');
        }
        echo "Send client pool";
        $pool = $this->clients_pool[$user_id];
        foreach ($pool as $_client) {
            if ($notAsweredclient) {
                if ($_client->name->window_id == $notAsweredclient->name->window_id) {
                    continue;
                }
                $_client->sendClient($_client, $msg);
            } else {
                $_client->sendClient($_client, $msg);
            }
        }
    }

    public function sendAllClients(Message $msg, ConnectionInterface $notAsweredclient = null)
    {
        foreach ($this->clients_pool as $pool) {
            foreach ($pool as $_client) {
                if ($notAsweredclient) {
                    if ($_client->name->window_id == $notAsweredclient->name->window_id) {
                        continue;
                    }
                    $_client->sendClient($_client, $msg);
                } else {
                    $_client->sendClient($_client, $msg);
                }
            }
        }
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

    public function getClientsIds()
    {
        $ids = [];
        foreach ($this->clients_pool as $key => $value) {
            $ids[] = $key;
        }

        return $ids;
    }
}
