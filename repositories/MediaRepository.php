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

	/**
	 * @throws ModelNotFoundException
	 */
	public function findModelByIdAndCreatedByOrUserId(int $id, int $userId, string $createdByType): Media
	{
		return Media::find()
		               ->byId($id)
		               ->notDeleted()
		               ->andWhere([
			               'OR',
			               ['user_id' => $userId],
			               [
				               'AND',
				               ['=', 'created_by_id', $userId],
				               ['=', 'created_by_type', $createdByType],
			               ]
		               ])
		               ->oneOrThrow();
	}
}