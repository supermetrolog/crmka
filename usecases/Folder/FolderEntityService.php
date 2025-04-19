<?php

declare(strict_types=1);

namespace app\usecases\Folder;

use app\dto\Folder\FolderEntityDto;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\FolderEntity;
use Throwable;
use yii\db\StaleObjectException;

class FolderEntityService
{
	private const DEFAULT_SORT_ORDER = 1.0;

	/**
	 * @throws SaveModelException
	 */
	public function create(FolderEntityDto $dto): FolderEntity
	{
		$entity = new FolderEntity([
			'folder_id'   => $dto->folder->id,
			'entity_id'   => $dto->entity_id,
			'entity_type' => $dto->entity_type,
			'sort_order'  => self::DEFAULT_SORT_ORDER
		]);

		$entity->saveOrThrow();

		return $entity;
	}

	/**
	 * @throws StaleObjectException
	 * @throws Throwable
	 */
	public function delete(FolderEntity $entity): void
	{
		$entity->delete();
	}

	// TODO: Сортировка
}