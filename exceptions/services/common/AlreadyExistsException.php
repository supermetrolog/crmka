<?php

namespace app\exceptions\services\common;

use Throwable;
use yii\base\Exception;

class AlreadyExistsException extends Exception
{
	public function __construct(string $whatExists = "Value", string $message = " already exists", $code = 0, Throwable $previous = null)
	{
		$message = $whatExists . $message;

		parent::__construct($message, $code, $previous);
	}
}