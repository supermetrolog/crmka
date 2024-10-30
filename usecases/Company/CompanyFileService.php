<?php

declare(strict_types=1);

namespace app\usecases\Company;

use app\components\Media\Media as MediaComponent;
use app\components\Media\SaveMediaErrorException;
use app\dto\Company\CreateCompanyFileDto;
use app\kernel\common\database\interfaces\transaction\TransactionBeginnerInterface;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\miniModels\CompanyFile;
use Throwable;
use yii\db\StaleObjectException;

class CompanyFileService
{
	private TransactionBeginnerInterface $transactionBeginner;
	private MediaComponent               $mediaComponent;

	public function __construct(
		TransactionBeginnerInterface $transactionBeginner,
		MediaComponent $mediaComponent
	)
	{
		$this->transactionBeginner = $transactionBeginner;
		$this->mediaComponent      = $mediaComponent;
	}

	/**
	 * @param CreateCompanyFileDto $dto
	 *
	 * @return CompanyFile
	 * @throws Throwable
	 * @throws SaveMediaErrorException
	 * @throws SaveModelException
	 */
	public function create(CreateCompanyFileDto $dto): CompanyFile
	{
		$tx = $this->transactionBeginner->begin();

		try {
			$name = md5($dto->file->name . time());
			$path = "$name.{$dto->file->extension}";

			$model = new CompanyFile([
				'company_id' => $dto->company_id,
				'name'       => $name,
				'type'       => $dto->file->type,
				'filename'   => $path,
				'size'       => (string)$dto->file->size,
			]);

			$model->saveOrThrow();

			$this->mediaComponent->put($path, $dto->file);

			$tx->commit();

			return $model;
		} catch (Throwable $th) {
			$tx->rollBack();
			throw $th;
		}
	}

	/**
	 * @param CompanyFile $model
	 *
	 * @return void
	 * @throws Throwable
	 * @throws StaleObjectException
	 */
	public function delete(CompanyFile $model): void
	{
		$model->delete();
		$this->mediaComponent->delete($model->filename);
	}
}