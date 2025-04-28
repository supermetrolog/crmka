<?php

declare(strict_types=1);

namespace app\usecases\Company;

use app\components\EventManager;
use app\components\Media\SaveMediaErrorException;
use app\dto\Company\CompanyActivityGroupDto;
use app\dto\Company\CompanyActivityProfileDto;
use app\dto\Company\CompanyDto;
use app\dto\Company\CompanyMediaDto;
use app\dto\Company\CompanyMiniModelsDto;
use app\dto\Company\CreateCompanyFileDto;
use app\dto\Company\DisableCompanyDto;
use app\dto\Media\CreateMediaDto;
use app\events\Company\ChangeConsultantCompanyEvent;
use app\events\Company\DisableCompanyEvent;
use app\events\Company\EnableCompanyEvent;
use app\events\NotificationEvent;
use app\helpers\ArrayHelper;
use app\kernel\common\database\interfaces\transaction\TransactionBeginnerInterface;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\Category;
use app\models\Company;
use app\models\CompanyActivityGroup;
use app\models\CompanyActivityProfile;
use app\models\Media;
use app\models\miniModels\CompanyFile;
use app\models\Notification;
use app\models\Productrange;
use app\models\User;
use app\usecases\Media\CreateMediaService;
use app\usecases\Media\MediaService;
use ErrorException;
use Throwable;
use Yii;
use yii\db\StaleObjectException;
use yii\helpers\ArrayHelper as YiiArrayHelper;
use yii\web\UploadedFile;

class CompanyService
{
	private TransactionBeginnerInterface $transactionBeginner;

	private CompanyFileService $companyFileService;
	private EventManager       $eventManager;

	private CompanyActivityGroupService   $companyActivityGroupService;
	private CompanyActivityProfileService $companyActivityProfileService;

	private CreateMediaService $createMediaService;

	private MediaService $mediaService;

	public function __construct(
		TransactionBeginnerInterface $transactionBeginner,
		CompanyFileService $companyFileService,
		CreateMediaService $createMediaService,
		MediaService $mediaService,
		EventManager $eventManager,
		CompanyActivityGroupService $companyActivityGroupService,
		CompanyActivityProfileService $companyActivityProfileService
	)
	{
		$this->transactionBeginner           = $transactionBeginner;
		$this->companyFileService            = $companyFileService;
		$this->createMediaService            = $createMediaService;
		$this->mediaService                  = $mediaService;
		$this->eventManager                  = $eventManager;
		$this->companyActivityGroupService   = $companyActivityGroupService;
		$this->companyActivityProfileService = $companyActivityProfileService;
	}

	/**
	 * @param CompanyDto           $dto
	 * @param CompanyMiniModelsDto $miniModelsDto
	 * @param CompanyMediaDto      $mediaDto
	 *
	 * @return Company
	 * @throws SaveModelException
	 * @throws Throwable
	 * @throws SaveMediaErrorException
	 */
	public function create(CompanyDto $dto, CompanyMiniModelsDto $miniModelsDto, CompanyMediaDto $mediaDto): Company
	{
		$tx = $this->transactionBeginner->begin();

		try {
			$model = new Company([
				'nameEng'              => $dto->nameEng,
				'nameRu'               => $dto->nameRu,
				'nameBrand'            => $dto->nameBrand,
				'noName'               => $dto->noName,
				'formOfOrganization'   => $dto->formOfOrganization,
				'companyGroup_id'      => $dto->companyGroup_id,
				'officeAdress'         => $dto->officeAdress,
				'status'               => $dto->status,
				'consultant_id'        => $dto->consultant_id,
				'legalAddress'         => $dto->legalAddress,
				'ogrn'                 => $dto->ogrn,
				'inn'                  => $dto->inn,
				'kpp'                  => $dto->kpp,
				'checkingAccount'      => $dto->checkingAccount,
				'correspondentAccount' => $dto->correspondentAccount,
				'inTheBank'            => $dto->inTheBank,
				'bik'                  => $dto->bik,
				'okved'                => $dto->okved,
				'okpo'                 => $dto->okpo,
				'signatoryName'        => $dto->signatoryName,
				'signatoryMiddleName'  => $dto->signatoryMiddleName,
				'signatoryLastName'    => $dto->signatoryLastName,
				'basis'                => $dto->basis,
				'documentNumber'       => $dto->documentNumber,
				'description'          => $dto->description,
				'passive_why'          => $dto->passive_why,
				'passive_why_comment'  => $dto->passive_why_comment,
				'rating'               => $dto->rating,
				'processed'            => $dto->processed,
				'is_individual'        => $dto->is_individual,
				'individual_full_name' => $dto->individual_full_name,
				'show_product_ranges'  => $dto->show_product_ranges
			]);

			$model->saveOrThrow();

			$this->createActivityGroups($model, $dto->activity_group_ids);
			$this->createActivityProfiles($model, $dto->activity_profile_ids);

			$model->createManyMiniModels([
				Category::class     => $miniModelsDto->categories,
				Productrange::class => $miniModelsDto->productRanges
			]);

			$this->saveFiles($model, $mediaDto->files);

			if ($mediaDto->logo) {
				$this->saveLogo($model, $mediaDto->logo);
			}

			// TODO: Переделать на EventManager
			$model->trigger(
				Company::COMPANY_CREATED_EVENT,
				new NotificationEvent([
					'consultant_id' => $model->consultant_id,
					'type'          => Notification::TYPE_COMPANY_INFO,
					'title'         => 'компания',
					'body'          => Yii::$app->controller->renderFile('@app/views/notifications_template/assigned_company.php', ['model' => $model])
				])
			);

			$tx->commit();

			return $model;
		} catch (Throwable $th) {
			$tx->rollBack();
			throw $th;
		}
	}

	/**
	 * @param Company              $model
	 * @param CompanyDto           $dto
	 * @param CompanyMiniModelsDto $miniModelsDto
	 * @param CompanyMediaDto      $mediaDto
	 *
	 * @return Company
	 * @throws Throwable
	 */
	public function update(
		Company $model,
		CompanyDto $dto,
		CompanyMiniModelsDto $miniModelsDto,
		CompanyMediaDto $mediaDto
	): Company
	{
		$tx = $this->transactionBeginner->begin();

		try {
			$oldConsultantId = $model->consultant_id;

			$model->load([
				'nameEng'              => $dto->nameEng,
				'nameRu'               => $dto->nameRu,
				'nameBrand'            => $dto->nameBrand,
				'noName'               => $dto->noName,
				'formOfOrganization'   => $dto->formOfOrganization,
				'companyGroup_id'      => $dto->companyGroup_id,
				'officeAdress'         => $dto->officeAdress,
				'status'               => $dto->status,
				'consultant_id'        => $dto->consultant_id,
				'legalAddress'         => $dto->legalAddress,
				'ogrn'                 => $dto->ogrn,
				'inn'                  => $dto->inn,
				'kpp'                  => $dto->kpp,
				'checkingAccount'      => $dto->checkingAccount,
				'correspondentAccount' => $dto->correspondentAccount,
				'inTheBank'            => $dto->inTheBank,
				'bik'                  => $dto->bik,
				'okved'                => $dto->okved,
				'okpo'                 => $dto->okpo,
				'signatoryName'        => $dto->signatoryName,
				'signatoryMiddleName'  => $dto->signatoryMiddleName,
				'signatoryLastName'    => $dto->signatoryLastName,
				'basis'                => $dto->basis,
				'documentNumber'       => $dto->documentNumber,
				'description'          => $dto->description,
				'passive_why'          => $dto->passive_why,
				'passive_why_comment'  => $dto->passive_why_comment,
				'rating'               => $dto->rating,
				'processed'            => $dto->processed,
				'is_individual'        => $dto->is_individual,
				'individual_full_name' => $dto->individual_full_name,
				'show_product_ranges'  => $dto->show_product_ranges
			]);

			$model->saveOrThrow();

			$this->updateActivityGroups($model, $dto->activity_group_ids);
			$this->updateActivityProfiles($model, $dto->activity_profile_ids);

			$model->updateManyMiniModels([
				Category::class     => $miniModelsDto->categories,
				Productrange::class => $miniModelsDto->productRanges
			]);

			$this->deleteMissingFiles($model->files, $dto->files);
			$this->saveFiles($model, $mediaDto->files);

			$logoShouldBeDeleted = (!$dto->logo_id || $mediaDto->logo) && $model->logo;

			if ($logoShouldBeDeleted) {
				$this->deleteLogo($model);
			}

			if ($mediaDto->logo) {
				$this->saveLogo($model, $mediaDto->logo);
			}

			// TODO: Переделать на EventManager
			if ($oldConsultantId !== $model->consultant_id) {
				$oldConsultant = User::find()->byId($oldConsultantId)->one();
				$newConsultant = User::find()->byId($model->consultant_id)->one();

				$model->trigger(Company::COMPANY_CREATED_EVENT, new NotificationEvent([
					'consultant_id' => $oldConsultant->id,
					'type'          => Notification::TYPE_COMPANY_INFO,
					'title'         => 'компания',
					'body'          => Yii::$app->controller->renderFile('@app/views/notifications_template/unAssigned_company.php', ['model' => $model])
				]));

				$model->trigger(Company::COMPANY_CREATED_EVENT, new NotificationEvent([
					'consultant_id' => $model->consultant_id,
					'type'          => Notification::TYPE_COMPANY_INFO,
					'title'         => 'компания',
					'body'          => Yii::$app->controller->renderFile('@app/views/notifications_template/assigned_company.php', ['model' => $model])
				]));

				$this->eventManager->trigger(new ChangeConsultantCompanyEvent($model, $oldConsultant, $newConsultant));
			}

			$tx->commit();

			return $model;
		} catch (Throwable $th) {
			$tx->rollBack();
			throw $th;
		}
	}

	/**
	 * @param Company        $model
	 * @param UploadedFile[] $files
	 *
	 * @return void
	 * @throws SaveMediaErrorException
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	public function saveFiles(Company $model, array $files): void
	{
		foreach ($files as $file) {
			$this->companyFileService->create(new CreateCompanyFileDto([
				'company_id' => $model->id,
				'file'       => $file
			]));
		}
	}

	/**
	 * @param Company      $model
	 * @param UploadedFile $logo
	 *
	 * @return Media
	 * @throws Throwable
	 */
	public function saveLogo(Company $model, UploadedFile $logo): Media
	{
		return $this->createMediaService->create(new CreateMediaDto([
			'model_id'     => $model->id,
			'model_type'   => Company::getMorphClass(),
			'category'     => Company::LOGO_MEDIA_CATEGORY,
			'uploadedFile' => $logo,
			'mime_type'    => mime_content_type($logo->tempName)
		]));
	}

	/**
	 * @param CompanyFile[] $oldFiles
	 * @param array         $newFiles
	 *
	 * @return void
	 * @throws StaleObjectException
	 * @throws Throwable
	 */
	public function deleteMissingFiles(array $oldFiles, array $newFiles): void
	{
		$deletedFiles = ArrayHelper::diffByCallback(
			$oldFiles,
			$newFiles,
			static fn($oldFile, $newFile) => YiiArrayHelper::getValue($oldFile, 'id') - YiiArrayHelper::getValue($newFile, 'id')
		);

		foreach ($deletedFiles as $file) {
			$this->companyFileService->delete($file);
		}
	}

	/**
	 * @param Company $model
	 *
	 * @return void
	 * @throws StaleObjectException
	 * @throws Throwable
	 */
	public function delete(Company $model): void
	{
		$model->delete();
	}

	/**
	 * @throws StaleObjectException
	 * @throws Throwable
	 */
	public function deleteLogo(Company $model): void
	{
		$this->mediaService->delete($model->logo);
	}

	/**
	 * @throws StaleObjectException
	 * @throws Throwable
	 */
	public function updateLogo(Company $model, UploadedFile $logoFile): Media
	{
		$tx = $this->transactionBeginner->begin();

		try {
			if ($model->logo) {
				$this->deleteLogo($model);
			}

			$createdLogo = $this->saveLogo($model, $logoFile);

			$tx->commit();

			return $createdLogo;
		} catch (Throwable $th) {
			$tx->rollBack();
			throw $th;
		}
	}

	/**
	 * @param int[] $activityGroupIds
	 *
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	public function createActivityGroups(Company $company, array $activityGroupIds): void
	{
		$tx = $this->transactionBeginner->begin();

		try {
			foreach ($activityGroupIds as $activityGroupId) {
				$this->companyActivityGroupService->create(
					new CompanyActivityGroupDto([
						'company_id'        => $company->id,
						'activity_group_id' => $activityGroupId,
					])
				);
			}

			$tx->commit();
		} catch (Throwable $e) {
			$tx->rollBack();
			throw $e;
		}

	}

	/**
	 * @param int[] $activityProfileIds
	 *
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	public function createActivityProfiles(Company $company, array $activityProfileIds): void
	{
		$tx = $this->transactionBeginner->begin();

		try {
			foreach ($activityProfileIds as $activityProfileId) {
				$this->companyActivityProfileService->create(
					new CompanyActivityProfileDto([
						'company_id'          => $company->id,
						'activity_profile_id' => $activityProfileId,
					])
				);
			}

			$tx->commit();
		} catch (Throwable $e) {
			$tx->rollBack();
			throw $e;
		}
	}

	/**
	 * @param CompanyActivityGroup[] $activityGroups
	 *
	 * @throws StaleObjectException
	 * @throws Throwable
	 * @throws ErrorException
	 */
	private function deleteActivityGroups(array $activityGroups): void
	{
		$tx = $this->transactionBeginner->begin();

		try {
			foreach ($activityGroups as $group) {
				$this->companyActivityGroupService->delete($group);
			}

			$tx->commit();
		} catch (Throwable $e) {
			$tx->rollBack();
			throw $e;
		}
	}

	/**
	 * @param CompanyActivityProfile[] $activityProfiles
	 *
	 * @throws StaleObjectException
	 * @throws Throwable
	 * @throws ErrorException
	 */
	private function deleteActivityProfiles(array $activityProfiles): void
	{
		$tx = $this->transactionBeginner->begin();

		try {
			foreach ($activityProfiles as $profile) {
				$this->companyActivityProfileService->delete($profile);
			}

			$tx->commit();
		} catch (Throwable $e) {
			$tx->rollBack();
			throw $e;
		}
	}

	/**
	 * @param int[] $activityProfileIds
	 *
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	private function updateActivityProfiles(Company $company, array $activityProfileIds): void
	{
		$tx = $this->transactionBeginner->begin();

		try {
			$currentActivityProfiles = $company->companyActivityProfiles;

			$currentActivityProfileIds = ArrayHelper::column($currentActivityProfiles, 'activity_profile_id');
			$newActivityProfileIds     = ArrayHelper::diff($activityProfileIds, $currentActivityProfileIds);

			$this->createActivityProfiles($company, $newActivityProfileIds);

			$activityProfileIdsHashSet = ArrayHelper::flip($activityProfileIds);
			$deletedActivityProfiles   = ArrayHelper::filter($currentActivityProfiles, static fn(CompanyActivityProfile $profile) => !isset($activityProfileIdsHashSet[$profile->activity_profile_id]));

			$this->deleteActivityProfiles($deletedActivityProfiles);

			$tx->commit();
		} catch (Throwable $th) {
			$tx->rollback();
			throw $th;
		}
	}

	/**
	 * @param int[] $activityGroupIds
	 *
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	private function updateActivityGroups(Company $company, array $activityGroupIds): void
	{
		$tx = $this->transactionBeginner->begin();

		try {
			$currentActivityGroups = $company->companyActivityGroups;

			$currentActivityGroupIds = ArrayHelper::column($currentActivityGroups, 'activity_group_id');
			$newActivityGroupIds     = ArrayHelper::diff($activityGroupIds, $currentActivityGroupIds);

			$this->createActivityGroups($company, $newActivityGroupIds);

			$activityGroupIdsHashSet = ArrayHelper::flip($activityGroupIds);
			$deletedActivityGroups   = ArrayHelper::filter($currentActivityGroups, static fn(CompanyActivityGroup $group) => !isset($activityGroupIdsHashSet[$group->activity_group_id]));

			$this->deleteActivityGroups($deletedActivityGroups);

			$tx->commit();
		} catch (Throwable $th) {
			$tx->rollback();
			throw $th;
		}
	}

	/**
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	public function markAsPassive(Company $company, DisableCompanyDto $dto): void
	{
		if ($company->isPassive()) {
			return;
		}

		$tx = $this->transactionBeginner->begin();

		try {
			$company->status = Company::STATUS_PASSIVE;

			$company->passive_why         = $dto->passive_why;
			$company->passive_why_comment = $dto->passive_why_comment;

			$company->saveOrThrow();

			$this->eventManager->trigger(new DisableCompanyEvent($company));

			$tx->commit();
		} catch (Throwable $th) {
			$tx->rollback();
			throw $th;
		}
	}

	/**
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	public function markAsActive(Company $company): void
	{
		if ($company->isActive()) {
			return;
		}

		$tx = $this->transactionBeginner->begin();

		try {
			$company->status = Company::STATUS_ACTIVE;

			$company->passive_why         = null;
			$company->passive_why_comment = null;

			$company->saveOrThrow();

			$this->eventManager->trigger(new EnableCompanyEvent($company));

			$tx->commit();
		} catch (Throwable $th) {
			$tx->rollback();
			throw $th;
		}
	}
}