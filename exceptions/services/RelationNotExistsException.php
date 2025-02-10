<?php

namespace app\exceptions\services;

use Throwable;
use yii\base\Exception;

class RelationNotExistsException extends Exception
{
	public array $data = [];

	public function __construct(string $firstType, string $secondType, array $data = [],
		$code = 0, Throwable $previous = null)
	{
		$this->data = $data;

		$message = "Relation $firstType-$secondType not exists";
		parent::__construct($message, $code, $previous);
	}
}