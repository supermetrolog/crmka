<?php

namespace app\usecases\Company;

use app\dto\Company\CompanyActivityGroupDto;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\Company\CompanyActivityGroup;
use Throwable;
use yii\db\StaleObjectException;

class CompanyActivityGroupService
{
	/**
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	public function create(CompanyActivityGroupDto $dto): CompanyActivityGroup
	{
		$model = new CompanyActivityGroup([
			'company_id'        => $dto->company_id,
			'activity_group_id' => $dto->activity_group_id
		]);

		$model->saveOrThrow();

		return $model;
	}

	/**
	 * @throws Throwable
	 * @throws StaleObjectException
	 */
	public function delete(CompanyActivityGroup $model): void
	{
		$model->delete();
	}
}