<?php

namespace app\events\Company;


use app\dto\Company\ChangeCompanyConsultantDto;
use app\events\AbstractEvent;
use app\models\Company\Company;
use app\models\User;

/**
 * @property-read int $consultantId
 */
class ChangeConsultantCompanyEvent extends AbstractEvent
{
	private Company                    $company;
	private User                       $oldConsultant;
	private User                       $newConsultant;
	private ChangeCompanyConsultantDto $dto;

	public function __construct(Company $company, User $oldConsultant, User $newConsultant, ChangeCompanyConsultantDto $dto)
	{
		parent::__construct();

		$this->company       = $company;
		$this->oldConsultant = $oldConsultant;
		$this->newConsultant = $newConsultant;
		$this->dto           = $dto;
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

	public function getDto(): ChangeCompanyConsultantDto
	{
		return $this->dto;
	}
}
