<?php

declare(strict_types=1);

namespace app\actions\Company;

use app\dto\Company\DisableCompanyDto;
use app\kernel\common\actions\Action;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\Company\Company;
use app\models\Contact;
use app\usecases\Company\CompanyService;
use Throwable;
use yii\base\ErrorException;
use yii\db\Expression;

class EnforceCompanyStatusesAction extends Action
{
	private CompanyService $companyService;

	public function __construct($id, $module, CompanyService $companyService, $config = [])
	{
		$this->companyService = $companyService;

		parent::__construct($id, $module, $config);
	}

	/**
	 * @throws ErrorException
	 * @throws Throwable
	 * @throws SaveModelException
	 */
	public function run(): void
	{
		$contactQuery = Contact::find()
		                       ->select(['company_id', 'count' => new Expression('COUNT(*)')])
		                       ->andWhere([Contact::field('status') => Contact::STATUS_ACTIVE])
		                       ->andWhere([Contact::field('type') => Contact::DEFAULT_CONTACT_TYPE])
		                       ->groupBy('company_id');

		$query = Company::find()
		                ->active()
		                ->leftJoin(['cnts' => $contactQuery], 'cnts.company_id = company.id')
		                ->andWhere(['cnts.count' => null]);

		$passiveDto = new DisableCompanyDto([
			'passive_why' => Company::PASSIVE_WHY_NO_CONTACTS
		]);

		$this->info('Start enforcing company statuses');

		$count = 0;

		foreach ($query->each() as $company) {
			$this->companyService->markAsPassive($company, $passiveDto);
			$count++;

			$this->commentf('Changed status for company #%d', $company->id);
		}

		$this->infof('Enforcing finished, changed companies: %d', $count);
	}
}