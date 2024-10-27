<?php

declare(strict_types=1);

namespace app\usecases\Company;

use app\components\Media\SaveMediaErrorException;
use app\dto\Company\CompanyDto;
use app\dto\Company\CompanyMediaDto;
use app\dto\Company\CompanyMiniModelsDto;
use app\dto\Company\CreateCompanyFileDto;
use app\dto\Media\CreateMediaDto;
use app\events\NotificationEvent;
use app\helpers\ArrayHelper;
use app\kernel\common\database\interfaces\transaction\TransactionBeginnerInterface;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\Category;
use app\models\Company;
use app\models\Media;
use app\models\miniModels\CompanyFile;
use app\models\Notification;
use app\models\Productrange;
use app\usecases\Media\CreateMediaService;
use app\usecases\Media\MediaService;
use Throwable;
use Yii;
use yii\db\StaleObjectException;
use yii\helpers\ArrayHelper as YiiArrayHelper;
use yii\web\UploadedFile;

class CompanyService
{
	private TransactionBeginnerInterface $transactionBeginner;

	private CompanyFileService $companyFileService;

	private CreateMediaService $createMediaService;

	private MediaService $mediaService;

	public function __construct(
		TransactionBeginnerInterface $transactionBeginner,
		CompanyFileService $companyFileService,
		CreateMediaService $createMediaService,
		MediaService $mediaService
	)
	{
		$this->transactionBeginner = $transactionBeginner;
		$this->companyFileService  = $companyFileService;
		$this->createMediaService  = $createMediaService;
		$this->mediaService        = $mediaService;
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
				'activityGroup'        => $dto->activityGroup,
				'activityProfile'      => $dto->activityProfile,
				'description'          => $dto->description,
				'passive_why'          => $dto->passive_why,
				'passive_why_comment'  => $dto->passive_why_comment,
				'rating'               => $dto->rating,
				'processed'            => $dto->processed
			]);

			$model->saveOrThrow();

			$model->createManyMiniModels([
				Category::class     => $miniModelsDto->categories,
				Productrange::class => $miniModelsDto->productRanges
			]);

			$this->saveFiles($model, $mediaDto->files);

			if ($mediaDto->logo) {
				$this->saveLogo($model, $mediaDto->logo);
			}

//			$model->trigger(
//				Company::COMPANY_CREATED_EVENT,
//				new NotificationEvent([
//					'consultant_id' => $model->consultant_id,
//					'type'          => Notification::TYPE_COMPANY_INFO,
//					'title'         => 'компания',
//					'body'          => Yii::$app->controller->renderFile('@app/views/notifications_template/assigned_company.php', ['model' => $model])
//				])
//			);

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
				'activityGroup'        => $dto->activityGroup,
				'activityProfile'      => $dto->activityProfile,
				'description'          => $dto->description,
				'passive_why'          => $dto->passive_why,
				'passive_why_comment'  => $dto->passive_why_comment,
				'rating'               => $dto->rating,
				'processed'            => $dto->processed
			]);

			$model->saveOrThrow();

			$model->updateManyMiniModels([
				Category::class     => $miniModelsDto->categories,
				Productrange::class => $miniModelsDto->productRanges
			]);

			$this->deleteMissingFiles($model->files, $dto->files);
			$this->saveFiles($model, $mediaDto->files);

			$logoShouldBeDeleted = !$dto->logo_id || $mediaDto->logo;

			if ($logoShouldBeDeleted && $model->logo) {
				$this->mediaService->delete($model->logo);
			}

			if ($mediaDto->logo) {
				$this->saveLogo($model, $mediaDto->logo);
			}

			$model->trigger(Company::COMPANY_CREATED_EVENT, new NotificationEvent([
				'consultant_id' => $oldConsultantId,
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
}