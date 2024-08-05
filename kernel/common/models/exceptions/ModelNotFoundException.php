<?php

declare(strict_types=1);

namespace app\kernel\common\models\exceptions;

use Exception;
use Throwable;

class ModelNotFoundException extends Exception
{
	public function __construct($message = "The requested model does not found.", $code = 0, Throwable $previous = null)
	{
		parent::__construct($message, $code, $previous);
	}
}