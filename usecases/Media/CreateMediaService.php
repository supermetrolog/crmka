<?php

declare(strict_types=1);

namespace app\usecases\Media;

use app\dto\Media\CreateMediaDto;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\Media;
use yii\web\UploadedFile;

class CreateMediaService
{
	/**
	 * @throws SaveModelException
	 */
	public function create(CreateMediaDto $dto): Media
	{
		$name = md5($dto->uploadedFile->name . time()) . $dto->uploadedFile->extension;

		\Yii::$app->media->put($name, $dto->uploadedFile);

		$media = new Media([
			'name'          => $name,
			'original_name' => $dto->uploadedFile->name,
			'extension'     => $dto->uploadedFile->extension,
			'path'          => \Yii::$app->media->path($name),
			'category'      => $dto->category,
			'model_type'    => $dto->model_type,
			'model_id'      => $dto->model_id,
		]);

		$media->saveOrThrow();

		return $media;
	}
}
