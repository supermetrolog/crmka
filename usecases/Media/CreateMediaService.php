<?php

declare(strict_types=1);

namespace app\usecases\Media;

use app\dto\Media\CreateMediaDto;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\Media;

class CreateMediaService
{
	/**
	 * @throws SaveModelException
	 */
	public function create(CreateMediaDto $dto): Media
	{
		$media = new Media([
			'name'          => $dto->name,
			'original_name' => $dto->original_name,
			'extension'     => $dto->extension,
			'path'          => $dto->path,
			'category'      => $dto->category,
			'model_type'    => $dto->model_type,
			'model_id'      => $dto->model_id,
		]);

		$media->saveOrThrow();

		return $media;
	}
}
