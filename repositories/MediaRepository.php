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
	public function findModelByIdAndCreatedBy(int $id, int $createdById, string $createdByType): Media
	{
		return Media::find()
		               ->byId($id)
		               ->notDeleted()
		               ->byMorph($createdById, $createdByType)
		               ->oneOrThrow();
	}
}