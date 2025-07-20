<?php

namespace app\events\Deal;

use app\dto\Deal\CreateRequestDealDto;
use app\events\AbstractEvent;
use app\models\Deal;
use app\models\Request;

class CreateRequestDealEvent extends AbstractEvent
{
	private Request               $request;
	private Deal                  $deal;
	private ?CreateRequestDealDto $dto;

	public function __construct(Request $request, Deal $deal, ?CreateRequestDealDto $dto = null)
	{
		$this->request = $request;
		$this->deal    = $deal;
		$this->dto     = $dto;

		parent::__construct();
	}

	public function getRequest(): Request
	{
		return $this->request;
	}

	public function getDeal(): Deal
	{
		return $this->deal;
	}

	public function getDto(): ?CreateRequestDealDto
	{
		return $this->dto;
	}
}
