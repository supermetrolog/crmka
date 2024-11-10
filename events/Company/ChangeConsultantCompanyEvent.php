<?php

namespace app\events\Company;


use app\events\AbstractEvent;
use app\models\Company;
use app\models\User;

/**
 * @property-read int $consultantId
 */
class ChangeConsultantCompanyEvent extends AbstractEvent
{
	public Company $company;
	public User    $oldConsultant;
	public User    $newConsultant;

	public function __construct(Company $company, User $oldConsultant, User $newConsultant)
	{
		parent::__construct();

		$this->company       = $company;
		$this->oldConsultant = $oldConsultant;
		$this->newConsultant = $newConsultant;
	}

	public function getCompany(): Company
	{
		return $this->company;
	}

	public function getOldConsultant(): User
	{
		return $this->oldConsultant;
	}

	public function getNewConsultant(): User
	{
		return $this->newConsultant;
	}
}
