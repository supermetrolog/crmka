<?php

namespace app\events\Company;


use app\events\AbstractEvent;
use app\models\Company;

class EnableCompanyEvent extends AbstractEvent
{
	public Company $company;

	public function __construct(Company $company)
	{
		parent::__construct();

		$this->company = $company;
	}

	public function getCompany(): Company
	{
		return $this->company;
	}
}
