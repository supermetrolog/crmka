<?php

namespace app\usecases\Company;

use app\dto\Company\CompanyStatusHistoryDto;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\Company\CompanyStatusHistory;
use Throwable;

class CompanyStatusHistoryService
{
	/**
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	public function create(CompanyStatusHistoryDto $dto): CompanyStatusHistory
	{
		$model = new CompanyStatusHistory([
			'company_id'        => $dto->company->id,
			'status'            => $dto->status,
			'reason'            => $dto->reason,
			'comment'           => $dto->comment,
			'changed_by_id'     => $dto->changedBy->id ?? null,
			'changed_by_source' => $dto->changedBySource,
		]);

		$model->saveOrThrow();

		return $model;
	}
}