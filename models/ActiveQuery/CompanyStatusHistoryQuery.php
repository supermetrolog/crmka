<?php

declare(strict_types=1);

namespace app\models\ActiveQuery;

use app\kernel\common\models\AQ\AQ;
use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\models\Company\CompanyStatusHistory;
use yii\base\ErrorException;

class CompanyStatusHistoryQuery extends AQ
{
	/**
	 * @return CompanyStatusHistory[]
	 */
	public function all($db = null): array
	{
		return parent::all($db);
	}

	public function one($db = null): ?CompanyStatusHistory
	{
		/** @var ?CompanyStatusHistory */
		return parent::one($db);
	}

	/**
	 * @throws ModelNotFoundException
	 */
	public function oneOrThrow($db = null): CompanyStatusHistory
	{
		/** @var CompanyStatusHistory */
		return parent::oneOrThrow($db);
	}

	/**
	 * @throws ErrorException
	 */
	public function byCompanyId(int $companyId): self
	{
		return $this->andWhere([CompanyStatusHistory::field('company_id') => $companyId]);
	}
}