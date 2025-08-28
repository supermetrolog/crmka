<?php

namespace app\events\Company;


use app\dto\Company\DisableCompanyDto;
use app\events\AbstractEvent;
use app\models\Company\Company;
use app\models\User;

class DisableCompanyEvent extends AbstractEvent
{
	public Company           $company;
	public DisableCompanyDto $dto;
	public ?User             $initiator;

	public function __construct(Company $company, DisableCompanyDto $dto, ?User $initiator = null)
	{
		parent::__construct();

		$this->company   = $company;
		$this->initiator = $initiator;
		$this->dto       = $dto;
	}

	public function getCompany(): Company
	{
		return $this->company;
	}

	public function getInitiator(): ?User
	{
		return $this->initiator;
	}

	public function getDto(): DisableCompanyDto
	{
		return $this->dto;
	}
}
