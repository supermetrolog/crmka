<?php

declare(strict_types=1);

namespace app\repositories;

use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\kernel\common\repository\AbstractRepository;
use app\models\Company\CompanyStatusHistory;
use yii\base\ErrorException;

class CompanyStatusHistoryRepository extends AbstractRepository
{
	/**
	 * @throws ModelNotFoundException
	 */
	public function findOneOrThrow(int $id): CompanyStatusHistory
	{
		return CompanyStatusHistory::find()->byId($id)->oneOrThrow();
	}

	public function findOne(int $id): ?CompanyStatusHistory
	{
		return CompanyStatusHistory::find()->byId($id)->one();
	}

	/**
	 * @return CompanyStatusHistory[]
	 */
	public function findAll(): array
	{
		return CompanyStatusHistory::find()->orderBy(['id' => SORT_DESC])->all();
	}

	/**
	 * @return CompanyStatusHistory[]
	 * @throws ErrorException
	 */
	public function findAllByCompanyId(int $companyId): array
	{
		return CompanyStatusHistory::find()->byCompanyId($companyId)->with(['changedBy'])->orderBy(['id' => SORT_DESC])->all();
	}
}