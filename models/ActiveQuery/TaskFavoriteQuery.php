<?php

namespace app\models\ActiveQuery;

use app\kernel\common\models\AQ\AQ;
use app\kernel\common\models\AQ\SoftDeleteTrait;
use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\models\TaskFavorite;

class TaskFavoriteQuery extends AQ
{
	use SoftDeleteTrait;

	/**
	 * @return TaskFavorite[]
	 */
	public function all($db = null): array
	{
		return parent::all($db);
	}

	public function one($db = null): ?TaskFavorite
	{
		/** @var TaskFavorite */
		return parent::one($db);
	}

	/**
	 * @throws ModelNotFoundException
	 */
	public function oneOrThrow($db = null): TaskFavorite
	{
		/** @var TaskFavorite */
		return parent::oneOrThrow($db);
	}

	public function byUserId(int $userId): self
	{
		return $this->andWhere([$this->field('user_id') => $userId]);
	}

	public function byTaskId(int $taskId): self
	{
		return $this->andWhere([$this->field('task_id') => $taskId]);
	}

	public function byPrevId(int $prevId): self
	{
		return $this->andWhere([$this->field('prev_id') => $prevId]);
	}

	public function top(): self
	{
		return $this->andWhereNull($this->field('prev_id'));
	}
}
