<?php

declare(strict_types=1);

namespace app\usecases\Media;

use app\components\Media\Media as MediaComponent;
use app\dto\Media\CreateMediaDto;
use app\kernel\common\database\interfaces\transaction\TransactionBeginnerInterface;
use app\models\Media;
use Throwable;

class CreateMediaService
{
	private MediaComponent               $media;
	private TransactionBeginnerInterface $transactionBeginner;

	public function __construct(
		MediaComponent $media,
		TransactionBeginnerInterface $transactionBeginner
	)
	{
		$this->media               = $media;
		$this->transactionBeginner = $transactionBeginner;
	}

	/**
	 * @throws Throwable
	 */
	public function create(CreateMediaDto $dto): Media
	{
		$tx = $this->transactionBeginner->begin();

		try {
			$name = md5($dto->uploadedFile->name . time());
			$path = "{$name}.{$dto->uploadedFile->extension}";

			$media = new Media([
				'name'          => $name,
				'original_name' => $dto->uploadedFile->name,
				'extension'     => $dto->uploadedFile->extension,
				'path'          => $path,
				'category'      => $dto->category,
				'model_type'    => $dto->model_type,
				'model_id'      => $dto->model_id,
				'mime_type'     => mime_content_type($dto->uploadedFile->tempName),
			]);

			$media->saveOrThrow();

			$this->media->put($path, $dto->uploadedFile);

			$tx->commit();

			return $media;
		} catch (Throwable $th) {
			$tx->rollback();
			throw $th;
		}
	}
}
