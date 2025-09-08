<?php

namespace app\daemons\loops;

use app\daemons\Clients;
use yii\base\Model;

abstract class BaseLoop extends Model
{
	protected Clients $clients;

	public function run(Clients $clients)
	{
		$this->clients = $clients;

		return $this->processed();
	}

	protected function changeIndex($array, $index): array
	{
		$newArray = [];

		foreach ($array as $value) {
			$newArray[$value[$index]][] = $value;
		}

		return $newArray;
	}

	abstract public function processed();
}
