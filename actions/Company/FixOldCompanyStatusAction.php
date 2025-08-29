<?php

declare(strict_types=1);

namespace app\actions\Company;

use app\dto\Company\DeleteCompanyDto;
use app\enum\Company\CompanyStatusEnum;
use app\kernel\common\actions\Action;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\Company\Company;
use app\usecases\Company\CompanyService;
use Throwable;
use yii\base\ErrorException;

class FixOldCompanyStatusAction extends Action
{
	protected CompanyService $companyService;

	public function __construct($id, $controller, CompanyService $companyService, $config = [])
	{
		$this->companyService = $companyService;

		parent::__construct($id, $controller, $config);
	}

	/**
	 * @throws ErrorException
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	public function run(): void
	{
		$this->info('Start fixing company old statuses from PASSIVE to DELETED');

		$query = Company::find()
		                ->byStatus(CompanyStatusEnum::PASSIVE)
		                ->byPassiveWhy([Company::PASSIVE_WHY_BLOCKED, Company::PASSIVE_WHY_DESTROYED, Company::PASSIVE_WHY_INCORRECT]);

		$count = 0;

		/** @var Company $company */
		foreach ($query->each() as $company) {
			$this->changeCompanyStatus($company);

			$this->commentf('Changed company #%d', $company->id);

			$count++;
		}

		if ($count > 0) {
			$this->infof('Process finished. Changed companies: %d', $count);
		} else {
			$this->info('Process finished. Nothing to change');
		}

	}

	/**
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	private function changeCompanyStatus(Company $company): void
	{
		$this->companyService->markAsDeleted($company, new DeleteCompanyDto([
			'passive_why' => $company->passive_why,
			'comment'     => $company->passive_why_comment,
		]));
	}
}