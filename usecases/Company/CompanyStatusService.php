<?php

namespace app\usecases\Company;

use app\dto\Company\DisableCompanyDto;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\Company\Company;
use Throwable;
use yii\base\ErrorException;

class CompanyStatusService
{
	private CompanyService $companyService;

	public function __construct(
		CompanyService $companyService
	)
	{
		$this->companyService = $companyService;
	}

	/**
	 * @throws ErrorException
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	public function archiveIfNeeded(Company $company): void
	{
		if (!$company->isActive()) {
			return;
		}

		$shouldBeArchived = (int)$company->getActiveContacts()->count() === 0;

		if (!$shouldBeArchived) {
			return;
		}

		$dto = new DisableCompanyDto([
			'passive_why' => Company::PASSIVE_WHY_NO_CONTACTS
		]);

		$this->companyService->markAsPassive($company, $dto);
	}

	/**
	 * @throws ErrorException
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	public function activeIfNeeded(Company $company): void
	{
		if ($company->isActive()) {
			return;
		}

		$shouldBeActives = (int)$company->getActiveContacts()->count() > 0;

		if (!$shouldBeActives) {
			return;
		}

		$this->companyService->markAsActive($company);
	}
}