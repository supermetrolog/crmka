<?php

namespace app\exceptions\services;

use Throwable;
use yii\base\Exception;

class UserHasInactiveStatusException extends Exception
{
	public function __construct($message = "User has inactive status", $code = 0, Throwable $previous = null)
	{
		parent::__construct($message, $code, $previous);
	}
}