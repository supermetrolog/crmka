<?php

declare(strict_types=1);

namespace app\commands;

use app\helpers\ArrayHelper;
use app\kernel\common\controller\ConsoleController;
use app\models\Company;
use app\models\Contact;
use yii\base\ErrorException;
use yii\db\Exception;

class NormalizeDataController extends ConsoleController
{
	/**
	 * @throws ErrorException
	 * @throws Exception
	 */
	public function actionCompanyStatus(): void
	{
		$query = Company::find()
		                ->joinWith(['activeContacts'])
		                ->andWhere(['is', Contact::field('id'), null])
		                ->andWhere([Company::field('status') => Company::STATUS_ACTIVE]);

		$companiesCount = (int)$query->count();

		$this->infof("Total companies without active contacts: %d", $companiesCount);

		/** @var Company[] $chunk */
		foreach ($query->batch() as $chunk) {
			$ids = ArrayHelper::column($chunk, 'id');

			Company::getDb()->createCommand()->update(Company::getTable(), ['status' => Company::STATUS_WITHOUT_ACTIVE_CONTACTS], ['id' => $ids])->execute();

			foreach ($chunk as $company) {
				$this->infof("Company #%d status changed from [%s] to [%s]", $company->id, $company->status, Company::STATUS_WITHOUT_ACTIVE_CONTACTS);
			}

		}

		$this->infof('Complete. Updated %d companies', $companiesCount);
	}
}