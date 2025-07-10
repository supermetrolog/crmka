<?php

declare(strict_types=1);

namespace app\usecases\Utility;

use app\dto\Request\ChangeRequestConsultantDto;
use app\dto\Utilities\FixObjectPurposesUtilitiesDto;
use app\dto\Utilities\TransferCompaniesToConsultantUtilitiesDto;
use app\kernel\common\database\interfaces\transaction\TransactionBeginnerInterface;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\Company;
use app\models\User;
use app\usecases\Object\ObjectService;
use app\usecases\Request\RequestService;
use Throwable;
use yii\base\ErrorException;

class UtilitiesService
{
	private ObjectService                $objectService;
	private TransactionBeginnerInterface $transactionBeginner;
	private RequestService               $requestService;

	public function __construct(
		ObjectService $objectService,
		TransactionBeginnerInterface $transactionBeginner,
		RequestService $requestService
	)
	{
		$this->objectService       = $objectService;
		$this->transactionBeginner = $transactionBeginner;
		$this->requestService      = $requestService;
	}

	/**
	 * @throws SaveModelException
	 */
	public function fixLandObjectPurposes(FixObjectPurposesUtilitiesDto $dto): void
	{
		$this->objectService->fixLandObjectPurposes($dto->object, $dto->purposes);
	}

	/**
	 * @throws ErrorException
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	public function transferCompaniesToConsultant(User $consultant, TransferCompaniesToConsultantUtilitiesDto $dto): void
	{
		$tx = $this->transactionBeginner->begin();

		try {
			foreach ($dto->companyIds as $companyId) {
				$company = Company::find()->byId($companyId)->one();

				if ($company) {
					$this->transferCompanyToConsultant($company, $consultant);
				}
			}

			$tx->commit();
		} catch (Throwable $e) {
			$tx->rollBack();
			throw $e;
		}
	}

	/**
	 * @throws SaveModelException
	 * @throws Throwable
	 * @throws ErrorException
	 */
	public function transferCompanyToConsultant(Company $company, User $consultant): void
	{
		$tx = $this->transactionBeginner->begin();

		try {
			$company->updateAttributes(['consultant_id' => $consultant->id]);

			$requests = $company->activeRequests;

			foreach ($requests as $request) {
				$this->requestService->changeConsultant($request, new ChangeRequestConsultantDto(['consultant' => $consultant]));
			}

			$tx->commit();
		} catch (Throwable $th) {
			$tx->rollBack();
			throw $th;
		}
	}
}