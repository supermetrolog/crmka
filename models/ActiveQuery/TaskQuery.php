<?php

namespace app\models\ActiveQuery;

use app\kernel\common\models\AQ;
use app\models\Task;
use yii\db\ActiveRecord;

class TaskQuery extends AQ
{

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

	public function byId(int $id): self
	{
		return $this->andWhere([$this->field('id') => $id]);
	}
}
