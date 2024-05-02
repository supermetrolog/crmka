<?php

declare(strict_types=1);

namespace app\kernel\web\http\responses;

class SuccessResponse extends Response
{
	public function __construct(?string $message = null)
	{
		parent::__construct(true, $message);
	}
}