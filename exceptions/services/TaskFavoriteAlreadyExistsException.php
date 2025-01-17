<?php

namespace app\exceptions\services;

use yii\base\Exception;

class TaskFavoriteAlreadyExistsException extends Exception
{
	public function __construct($message = "TaskFavorite already exists", $code = 0, Throwable $previous = null)
	{
		parent::__construct($message, $code, $previous);
	}
}