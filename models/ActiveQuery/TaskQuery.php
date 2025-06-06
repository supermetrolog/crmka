<?php

namespace app\models\ActiveQuery;

use app\kernel\common\models\AQ\AQ;
use app\kernel\common\models\AQ\SoftDeleteTrait;
use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\models\Task;
use yii\base\ErrorException;
use yii\db\ActiveRecord;
use yii\db\Expression;

class TaskQuery extends AQ
{
	use SoftDeleteTrait;

	/**
	 * @return Task[]|ActiveRecord[]
	 */
	public function all($db = null): array
	{
		return parent::all($db);
	}

	/**
	 * @return Task|ActiveRecord|null
	 */
	public function one($db = null): ?Task
	{
		return parent::one($db);
	}

	/**
	 * @return Task|ActiveRecord|null
	 * @throws ModelNotFoundException
	 */
	public function oneOrThrow($db = null): Task
	{
		return parent::oneOrThrow($db);
	}

	public function byStatus(int $status): self
	{
		return $this->andWhere([$this->field('status') => $status]);
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

	public function expired(): self
	{
		return $this->andWhereExpr($this->field('end'), 'NOW()', '<');
	}

	public function notExpired(): self
	{
		return $this->andWhere([
			'OR',
			['>', $this->field('end'), new Expression('NOW()')],
			['IS', $this->field('end'), null],
		]);
	}

	public function completed(): self
	{
		return $this->andWhere([$this->field('status') => Task::STATUS_DONE]);
	}

	public function notCompleted(): self
	{
		return $this->andWhere(['!=', $this->field('status'), Task::STATUS_DONE]);
	}

	public function impossible(): self
	{
		return $this->andWhere([$this->field('status') => Task::STATUS_IMPOSSIBLE]);
	}

	public function notImpossible(): self
	{
		return $this->andWhere(['!=', $this->field('status'), Task::STATUS_IMPOSSIBLE]);
	}

	/**
	 * @throws ErrorException
	 */
	public function byUserId(int $userId): self
	{
		return $this->andWhere([Task::field('user_id') => $userId]);
	}
}
