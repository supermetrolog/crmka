<?php

namespace app\models\ActiveQuery;

use app\kernel\common\models\AQ\AQ;
use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\models\FolderEntity;
use yii\base\ErrorException;

class FolderEntityQuery extends AQ
{
	/**
	 * @return FolderEntity[]
	 */
	public function all($db = null): array
	{
		return parent::all($db);
	}

	public function one($db = null): ?FolderEntity
	{
		/** @var ?FolderEntity */
		return parent::one($db);
	}

	/**
	 * @throws ModelNotFoundException
	 */
	public function oneOrThrow($db = null): FolderEntity
	{
		/** @var FolderEntity */
		return parent::oneOrThrow($db);
	}

	/**
	 * @throws ErrorException
	 */
	public function byEntityType(string $type): self
	{
		return $this->andWhere([FolderEntity::field('entity_type') => $type]);
	}

	/**
	 * @param string|int $id
	 *
	 * @throws ErrorException
	 */
	public function byEntityId($id): self
	{
		return $this->andWhere([FolderEntity::field('entity_id') => $id]);
	}

	public function byType(string $type): self
	{
		return $this->andWhere([FolderEntity::field('entity_type') => $type]);
	}
}
