<?php

namespace app\daemons;

use Ratchet\ConnectionInterface;
use yii\base\Model;
use yii\helpers\ArrayHelper;

class Clients extends Model
{
    private $clients_pool = [];

    public function setClient(ConnectionInterface $client)
    {
        $this->clients_pool[$client->name->user_id][] = $client;
        echo "Setted User\n";
        var_dump($this->clients_pool);
    }

    public function removeClient(ConnectionInterface $client)
    {
        if (!$this->clientExist($client)) {
            echo "Do not removed client because him not exist!";
            return false;
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
        return true;
    }

    public function sendClient(ConnectionInterface $client, Message $msg)
    {
        echo "Send Client!" . $msg->getData() . "\n";
        return $client->send($msg->getData());
    }
    public function sendClientPool(int $user_id, Message $msg, ConnectionInterface $notAsweredclient = null)
    {
        if (!ArrayHelper::keyExists($user_id, $this->clients_pool)) {
            echo "Not exist pool!\n";
            return false;
        }
        echo "Send client pool\n";
        $pool = $this->clients_pool[$user_id];
        var_dump(count($this->clients_pool[$user_id]));
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
        echo "Send ALl Clients\n";
        foreach ($this->clients_pool as $user_id => $pool) {
            $this->sendClientPool($user_id, $msg);
        }
        echo "Sended ALl Clients\n";
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
