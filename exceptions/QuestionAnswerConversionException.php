<?php

namespace app\exceptions;

use Throwable;
use yii\base\Exception;

class QuestionAnswerConversionException extends Exception
{
	public function __construct($type, $message = null, $code = 0, Throwable $previous = null)
	{
		if (is_null($message)) {
			$message = sprintf('Answer with this field cannot be converted to %s', $type);
		}

		parent::__construct($message, $code, $previous);
	}
}
