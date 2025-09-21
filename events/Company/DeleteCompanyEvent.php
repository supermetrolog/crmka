<?php

namespace app\events\Company;


use app\dto\Company\DeleteCompanyDto;
use app\events\AbstractEvent;
use app\models\Company\Company;
use app\models\User\User;

class DeleteCompanyEvent extends AbstractEvent
{
	public Company          $company;
	public DeleteCompanyDto $dto;
	public ?User            $initiator;

	public function __construct(Company $company, DeleteCompanyDto $dto)
	{
		parent::__construct();

		$this->company   = $company;
		$this->initiator = $dto->initiator;
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

	public function getDto(): DeleteCompanyDto
	{
		return $this->dto;
	}
}
