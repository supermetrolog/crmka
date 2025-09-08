<?php

namespace app\events\Company;


use app\events\AbstractEvent;
use app\models\Company\Company;
use app\models\User;

class ConsultantCompanyAssignedEvent extends AbstractEvent
{
	private Company $company;
	private User    $consultant;

	public function __construct(Company $company, User $consultant)
	{
		parent::__construct();

		$this->company    = $company;
		$this->consultant = $consultant;
	}

	public function getCompany(): Company
	{
		return $this->company;
	}

	public function getConsultant(): User
	{
		return $this->consultant;
	}
}
