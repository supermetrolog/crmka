<?php

namespace app\events\Request;

use app\events\AbstractEvent;
use app\models\Request;

class RequestCreatedEvent extends AbstractEvent
{
	public Request $request;

	public function __construct(Request $request)
	{
		$this->request = $request;

		parent::__construct();
	}

	public function getRequest(): Request
	{
		return $this->request;
	}
}
