<?php

declare(strict_types=1);

namespace app\usecases\Media;

use app\dto\Media\CreateMediaDto;
use app\kernel\common\database\interfaces\transaction\TransactionBeginnerInterface;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\Media;
use app\components\Media as MediaComponent;
use yii\web\UploadedFile;

class CreateMediaService
{
	private MediaComponent $media;
	private TransactionBeginnerInterface $transactionBeginner;

	public function __construct(
		MediaComponent $media,
		TransactionBeginnerInterface $transactionBeginner
	)
	{
		$this->media = $media;
		$this->transactionBeginner = $transactionBeginner;
	}

	/**
	 * @throws \Throwable
	 */
	public function create(CreateMediaDto $dto): Media
	{
		$tx = $this->transactionBeginner->begin();

		try {
			$name = md5($dto->uploadedFile->name . time());
			$path = $this->media->pathBuilder()->join($dto->path, "{$name}.{$dto->uploadedFile->extension}");

			$media = new Media([
				'name'          => $name,
				'original_name' => $dto->uploadedFile->name,
				'extension'     => $dto->uploadedFile->extension,
				'path'          => $path,
				'category'      => $dto->category,
				'model_type'    => $dto->model_type,
				'model_id'      => $dto->model_id,
			]);

			$media->saveOrThrow();

			$this->media->put($path, $dto->uploadedFile);

			$tx->commit();

			return $media;
		} catch (\Throwable $th) {
			$tx->rollback();
			throw $th;
		}
	}
}
