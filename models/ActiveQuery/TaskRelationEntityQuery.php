<?php

namespace app\models\ActiveQuery;

use app\kernel\common\models\AQ\AQ;
use app\kernel\common\models\AQ\SoftDeleteTrait;
use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\models\TaskRelationEntity;

class TaskRelationEntityQuery extends AQ
{
	use SoftDeleteTrait;

	/**
	 * @return TaskRelationEntity[]
	 */
	public function all($db = null): array
	{
		return parent::all($db);
	}

	public function one($db = null): ?TaskRelationEntity
	{
		/** @var ?TaskRelationEntity */
		return parent::one($db);
	}

	/**
	 * @throws ModelNotFoundException
	 */
	public function oneOrThrow($db = null): TaskRelationEntity
	{
		/** @var TaskRelationEntity */
		return parent::oneOrThrow($db);
	}

	public function byTaskId(int $taskId): self
	{
		return $this->andWhere([$this->field('task_id') => $taskId]);
	}

	public function byEntity(int $entityId, string $entityType): self
	{
		return $this->byEntityType($entityType)->byEntityId($entityId);
	}

	public function byEntityId(int $entityId): self
	{
		return $this->andWhere([$this->field('entity_id') => $entityId]);
	}

	public function byEntityType(string $entityType): self
	{
		return $this->andWhere([$this->field('entity_type') => $entityType]);
	}
}
