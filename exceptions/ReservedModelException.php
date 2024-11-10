<?php

namespace app\exceptions;

use Throwable;
use yii\web\ForbiddenHttpException;

class ReservedModelException extends ForbiddenHttpException
{
	public string $statusText = "Модель зарезервирована";

	public function __construct($message = 'Модель зарезервирована и не может быть удалена', Throwable $previous = null)
	{
		parent::__construct($message, $previous);
	}

	public function getName(): string
	{
		return $this->statusText;
	}
}
