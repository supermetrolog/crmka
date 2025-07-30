<?php

declare(strict_types=1);

namespace app\usecases\Company;

use app\components\Media\SaveMediaErrorException;
use app\dto\Company\CompanyContactsDto;
use app\dto\Company\CompanyDto;
use app\dto\Company\CompanyMediaDto;
use app\dto\Company\CompanyMiniModelsDto;
use app\dto\Phone\PhoneDto;
use app\kernel\common\database\interfaces\transaction\TransactionBeginnerInterface;
use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\Company;
use Throwable;
use yii\db\StaleObjectException;

class CompanyWithGeneralContactService
{
	private TransactionBeginnerInterface $transactionBeginner;

	private CompanyGeneralContactService $companyGeneralContactService;

	private CompanyService $companyService;

	public function __construct(
		TransactionBeginnerInterface $transactionBeginner,
		CompanyGeneralContactService $companyGeneralContactService,
		CompanyService $companyService
	)
	{
		$this->transactionBeginner          = $transactionBeginner;
		$this->companyGeneralContactService = $companyGeneralContactService;
		$this->companyService               = $companyService;
	}

	/**
	 * @param PhoneDto[] $phoneDtos
	 *
	 * @throws SaveMediaErrorException
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	public function create(
		CompanyDto $companyDto,
		CompanyMiniModelsDto $miniModelsDto,
		CompanyContactsDto $contactsDto,
		CompanyMediaDto $mediaDto,
		array $phoneDtos = []
	): Company
	{
		$tx = $this->transactionBeginner->begin();

		try {
			$company = $this->companyService->create($companyDto, $miniModelsDto, $mediaDto);

			$this->companyGeneralContactService->create($company, $contactsDto, $phoneDtos);

			$tx->commit();

			$company->refresh();

			return $company;
		} catch (Throwable $th) {
			$tx->rollBack();
			throw $th;
		}
	}

	/**
	 * @param PhoneDto[] $phoneDtos
	 *
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	public function update(
		Company $company,
		CompanyDto $companyDto,
		CompanyMiniModelsDto $miniModelsDto,
		CompanyContactsDto $contactsDto,
		CompanyMediaDto $companyMediaDto,
		array $phoneDtos = []
	): Company
	{
		$tx = $this->transactionBeginner->begin();

		try {
			$updatedCompany = $this->companyService->update(
				$company,
				$companyDto,
				$miniModelsDto,
				$companyMediaDto
			);

			$contact = $updatedCompany->generalContact;

			if ($contact) {
				$this->companyGeneralContactService->update($contact, $contactsDto);
			} else {
				$this->companyGeneralContactService->create($updatedCompany, $contactsDto, $phoneDtos);
			}

			$tx->commit();

			$updatedCompany->refresh();

			return $updatedCompany;
		} catch (Throwable $th) {
			$tx->rollBack();
			throw $th;
		}
	}

	/**
	 * @param Company $company
	 *
	 * @return void
	 * @throws Throwable
	 * @throws ModelNotFoundException
	 * @throws StaleObjectException
	 */
	public function delete(Company $company): void
	{
		$tx = $this->transactionBeginner->begin();

		try {
			$this->companyService->delete($company);
			$this->companyGeneralContactService->delete($company->generalContact);

			$tx->commit();
		} catch (Throwable $th) {
			$tx->rollBack();
			throw $th;
		}
	}
}