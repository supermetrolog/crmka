<?php

declare(strict_types=1);

namespace app\repositories;

use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\models\Folder;

class FolderRepository
{
	/**
	 * @throws ModelNotFoundException
	 */
	public function findOneOrThrow(int $id): Folder
	{
		return Folder::find()->byId($id)->oneOrThrow();
	}

	public function findOne(int $id): ?Folder
	{
		return Folder::find()->byId($id)->one();
	}
}