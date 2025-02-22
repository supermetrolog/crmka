<?php

declare(strict_types=1);

namespace app\actions\Company;

use app\dto\Company\CompanyActivityGroupDto;
use app\dto\Company\CompanyActivityProfileDto;
use app\helpers\ArrayHelper;
use app\kernel\common\actions\Action;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\Company;
use app\models\CompanyActivityGroup;
use app\models\CompanyActivityProfile;
use app\usecases\Company\CompanyActivityGroupService;
use app\usecases\Company\CompanyActivityProfileService;
use Throwable;
use yii\base\ErrorException;

class TransferCompanyActivityAction extends Action
{
	private CompanyActivityProfileService $companyActivityProfileService;
	private CompanyActivityGroupService   $companyActivityGroupService;

	public function __construct(
		$id,
		$controller,
		CompanyActivityProfileService $companyActivityProfileService,
		CompanyActivityGroupService $companyActivityGroupService,
		array $config = []
	)
	{
		$this->companyActivityProfileService = $companyActivityProfileService;
		$this->companyActivityGroupService   = $companyActivityGroupService;

		parent::__construct($id, $controller, $config);
	}

	/**
	 * @throws SaveModelException
	 * @throws ErrorException
	 * @throws Throwable
	 */
	public function run(): void
	{
		$query = Company::find()
		                ->distinct()
		                ->leftJoin(['cag' => CompanyActivityGroup::getTable()], [
			                'cag.company_id'        => Company::xfield('id'),
			                'cag.activity_group_id' => Company::xfield('activityGroup')
		                ])
		                ->leftJoin(['cap' => CompanyActivityProfile::getTable()], [
			                'cap.company_id'          => Company::xfield('id'),
			                'cap.activity_profile_id' => Company::xfield('activityProfile')
		                ])
		                ->andWhere([
			                'or',
			                ['cag.id' => null],
			                ['cap.id' => null]
		                ])
		                ->with(['companyActivityGroups', 'companyActivityProfiles']);

		$count = (int)$query->count();

		if ($count > 0) {
			/** @var Company $company */
			foreach ($query->each(500) as $company) {
				$isProcessed = false;

				if (!is_null($company->activityProfile) && !ArrayHelper::find($company->companyActivityProfiles, static fn($el) => $el->activity_profile_id === $company->activityProfile)) {
					$this->transferActivityProfile($company);
					$isProcessed = true;
				}

				if (!is_null($company->activityGroup) && !ArrayHelper::find($company->companyActivityGroups, static fn($el) => $el->activity_group_id === $company->activityGroup)) {
					$this->transferActivityGroup($company);
					$isProcessed = true;
				}

				if ($isProcessed) {
					$this->infof('Transfer company activity for company id: %d', $company->id);
				}

			}

			$this->comment("Data transfer completed for $count companies");
		} else {
			$this->comment('Data transfer not required');
		}

	}

	/**
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	private function transferActivityGroup(Company $company): void
	{
		$this->companyActivityGroupService->create(
			new CompanyActivityGroupDto([
				'company_id'        => $company->id,
				'activity_group_id' => $company->activityGroup
			])
		);
	}

	/**
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	private function transferActivityProfile(Company $company): void
	{
		$this->companyActivityProfileService->create(
			new CompanyActivityProfileDto([
				'company_id'          => $company->id,
				'activity_profile_id' => $company->activityProfile
			])
		);
	}
}