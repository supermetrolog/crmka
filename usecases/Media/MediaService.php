<?php

declare(strict_types=1);

namespace app\usecases\Media;

use app\components\Media as MediaComponent;
use app\dto\Media\UpdateMediaDto;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\Media;
use Throwable;
use yii\db\StaleObjectException;

class MediaService
{
	private MediaComponent $media;

	public function __construct(MediaComponent $media)
	{
		$this->media = $media;
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
	 * @throws StaleObjectException
	 * @throws Throwable
	 */
	public function delete(Media $media): void
	{
		$this->media->delete($media->path, $media->name, $media->extension);

		$media->delete();
	}
}
