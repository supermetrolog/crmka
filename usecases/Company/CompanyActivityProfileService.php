<?php

namespace app\usecases\Company;

use app\dto\Company\CompanyActivityProfileDto;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\CompanyActivityProfile;
use Throwable;
use yii\db\StaleObjectException;

class CompanyActivityProfileService
{
	/**
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	public function create(CompanyActivityProfileDto $dto): CompanyActivityProfile
	{
		$model = new CompanyActivityProfile([
			'company_id'          => $dto->company_id,
			'activity_profile_id' => $dto->activity_profile_id
		]);

		$model->saveOrThrow();

		return $model;
	}

	/**
	 * @throws Throwable
	 * @throws StaleObjectException
	 */
	public function delete(CompanyActivityProfile $model): void
	{
		$model->delete();
	}
}