<?php

namespace app\daemons\loops;

use yii\base\Model;

abstract class BaseLoop extends Model
{
    protected $clients = null;
    public function run($clients)
    {
        $this->clients = $clients;
        return $this->processed();
    }

    protected function getUsersIds()
    {
        $users_ids = [];
        foreach ($this->clients as $key => $value) {
            $users_ids[] = $key;
        }
        return $users_ids;
    }
    protected function changeIndex($array, $index)
    {
        $newArray = [];
        foreach ($array as $key => $value) {
            $newArray[$value[$index]][] = $value;
        }
        return $newArray;
    }
    abstract public function processed();
}
