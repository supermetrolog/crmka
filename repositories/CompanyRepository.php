<?php

declare(strict_types=1);

namespace app\repositories;

use app\models\Company;
use yii\base\ErrorException;

class CompanyRepository
{
	/**
	 * @return string[]
	 * @throws ErrorException
	 */
	public function getBankNameUniqueAll(): array
	{
		return Company::find()->distinct()->select(Company::field('inTheBank'))->andWhereNotNull('inTheBank')->column();
	}
}