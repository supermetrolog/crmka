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
        return array_keys($this->clients);
    }
    protected function changeIndex($array, $index)
    {
        $newArray = [];
        foreach ($array as $value) {
            $newArray[$value[$index]][] = $value;
        }
        return $newArray;
    }
    abstract public function processed();
}
