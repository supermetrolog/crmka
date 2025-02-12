<?php

declare(strict_types=1);

namespace app\usecases\Media;

use app\components\Media\Media as MediaComponent;
use app\kernel\common\database\interfaces\transaction\TransactionBeginnerInterface;
use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\models\Media;
use app\repositories\MediaRepository;
use Throwable;
use yii\db\StaleObjectException;

class MediaService
{
	private MediaComponent               $media;
	private MediaRepository              $repository;
	private TransactionBeginnerInterface $transactionBeginner;

	public function __construct(
		MediaComponent $media,
		MediaRepository $repository,
		TransactionBeginnerInterface $transactionBeginner
	)
	{
		$this->repository          = $repository;
		$this->media               = $media;
		$this->transactionBeginner = $transactionBeginner;
	}

//	/**
//	 * @throws SaveModelException
//	 */
//	public function update(Media $media, UpdateMediaDto $dto): Media
//	{
//		$media->load([
//			'name'          => $dto->name,
//			'original_name' => $dto->original_name,
//			'extension'     => $dto->extension,
//			'path'          => $dto->path,
//			'category'      => $dto->category,
//		]);
//
//		$media->saveOrThrow();
//
//		return $media;
//	}

	/**
	 * @param Media $media
	 *
	 * @return void
	 * @throws StaleObjectException
	 * @throws Throwable
	 */
	public function delete(Media $media): void
	{
		$tx = $this->transactionBeginner->begin();

		try {
			$media->delete();

			$this->media->delete($media->path);

			$tx->commit();
		} catch (Throwable $th) {
			$tx->rollback();
			throw $th;
		}
	}

	/**
	 * @throws ModelNotFoundException
	 */
	public function getById(int $id): Media
	{
		return $this->repository->findOneOrThrow($id);
	}
}
