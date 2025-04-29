<?php

namespace app\events\Company;


use app\events\AbstractEvent;
use app\models\Company;
use app\models\User;

class DisableCompanyEvent extends AbstractEvent
{
	public Company $company;
	public ?User   $initiator;

	public function __construct(Company $company, ?User $initiator)
	{
		parent::__construct();

		$this->company   = $company;
		$this->initiator = $initiator;
	}

	public function getCompany(): Company
	{
		return $this->company;
	}

	public function getInitiator(): ?User
	{
		return $this->initiator;
	}
}
