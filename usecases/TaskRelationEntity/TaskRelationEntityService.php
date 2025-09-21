<?php

declare(strict_types=1);

namespace app\usecases\TaskRelationEntity;

use app\dto\Task\CreateTaskRelationEntityDto;
use app\dto\TaskRelationEntity\UpdateTaskRelationEntityDto;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\TaskRelationEntity;
use app\models\User\User;
use Throwable;
use yii\db\StaleObjectException;

class TaskRelationEntityService
{
	/**
	 * @throws SaveModelException
	 */
	public function link(CreateTaskRelationEntityDto $dto): TaskRelationEntity
	{
		$existing = TaskRelationEntity::find()
		                              ->byTaskId($dto->task->id)
		                              ->byEntity($dto->entityId, $dto->entityType)
		                              ->notDeleted()
		                              ->one();

		if ($existing) {
			return $existing;
		}

		return $this->create($dto);
	}

	/**
	 * @throws SaveModelException
	 */
	public function create(CreateTaskRelationEntityDto $dto): TaskRelationEntity
	{
		$model = new TaskRelationEntity([
			'task_id'       => $dto->task->id,
			'entity_id'     => $dto->entityId,
			'entity_type'   => $dto->entityType,
			'relation_type' => $dto->relationType,
			'comment'       => $dto->comment,
			'created_by_id' => $dto->createdBy->id ?? null,
		]);

		$model->saveOrThrow();

		return $model;
	}

	/**
	 * @throws SaveModelException
	 */
	public function update(TaskRelationEntity $entity, UpdateTaskRelationEntityDto $dto): TaskRelationEntity
	{
		$entity->load([
			'comment' => $dto->comment
		]);

		$entity->saveOrThrow();

		return $entity;
	}

	/**
	 * @throws Throwable
	 * @throws StaleObjectException
	 */
	public function delete(TaskRelationEntity $entity, ?User $initiator = null): void
	{
		if ($entity->isDeleted()) {
			return;
		}

		if ($initiator) {
			$entity->deleted_by_id = $initiator->id;
		}

		$entity->delete();
	}
}