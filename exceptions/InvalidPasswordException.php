<?php

namespace app\exceptions;

use yii\web\HttpException;

class InvalidPasswordException extends HttpException
{
	public        $statusCode = 401;
	public string $statusText = "Неправильный пароль";

	public function __construct($message = null, $code = 1)
	{
		parent::__construct($this->statusCode, json_encode($message), $code);
	}

	public function getName(): string
	{
		return $this->statusText;
	}
}
