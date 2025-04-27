<?php

declare(strict_types=1);

namespace app\repositories;

use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\models\FolderEntity;

class FolderEntityRepository
{
	/**
	 * @throws ModelNotFoundException
	 */
	public function findOneOrThrow(int $id): FolderEntity
	{
		return FolderEntity::find()->byId($id)->oneOrThrow();
	}

	public function findOne(int $id): ?FolderEntity
	{
		return FolderEntity::find()->byId($id)->one();
	}
}