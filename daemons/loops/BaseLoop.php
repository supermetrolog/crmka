<?php

namespace app\daemons\loops;

use yii\base\Model;
use app\daemons\Clients;

abstract class BaseLoop extends Model
{
    protected Clients $clients;
    public function run(Clients $clients)
    {
        $this->clients = $clients;
        return $this->processed();
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
