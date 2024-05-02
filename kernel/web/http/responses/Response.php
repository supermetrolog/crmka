<?php

declare(strict_types=1);

namespace app\kernel\web\http\responses;

class Response
{
	public bool    $success;
	public ?string $message = null;

	public function __construct(bool $success, ?string $message = null)
	{
		$this->success = $success;
		$this->message = $message;
	}
}