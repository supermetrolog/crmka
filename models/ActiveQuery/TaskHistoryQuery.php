<?php

namespace app\models\ActiveQuery;

use app\kernel\common\models\AQ\AQ;
use app\kernel\common\models\AQ\SoftDeleteTrait;
use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\models\TaskHistory;

class TaskHistoryQuery extends AQ
{
	use SoftDeleteTrait;

	/** @return TaskHistory[] */
	public function all($db = null): array
	{
		return parent::all($db);
	}

	public function one($db = null): ?TaskHistory
	{
		/** @var TaskHistory */
		return parent::one($db);
	}

	/**
	 * @throws ModelNotFoundException
	 */
	public function oneOrThrow($db = null): TaskHistory
	{
		/** @var TaskHistory */
		return parent::oneOrThrow($db);
	}

	public function byCreatedById(int $id): self
	{
		return $this->andWhere([$this->field('created_by_id') => $id]);
	}

	public function byCreatedByType(string $type): self
	{
		return $this->andWhere([$this->field('created_by_type') => $type]);
	}

	public function byMorph(int $id, string $type): self
	{
		return $this->byCreatedByType($type)->byCreatedById($id);
	}

	public function byTaskId(int $id): self
	{
		return $this->andWhere([$this->field('task_id') => $id]);
	}

	public function byPreviousId(int $id): self
	{
		return $this->andWhere([$this->field('prev_id') => $id]);
	}
}
