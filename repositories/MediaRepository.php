<?php

declare(strict_types=1);

namespace app\repositories;

use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\models\Media;

class MediaRepository
{
	/**
	 * @throws ModelNotFoundException
	 */
	public function findModelByIdAndModel(int $id, int $modelId, string $modelType): Media
	{
		return Media::find()
		            ->byId($id)
		            ->notDeleted()
		            ->byMorph($modelId, $modelType)
		            ->oneOrThrow();
	}

	/**
	 * @throws ModelNotFoundException
	 */
	public function findOneOrThrow(int $id)
	{
		return Media::find()->notDeleted()->byId($id)->oneOrThrow();
	}
}