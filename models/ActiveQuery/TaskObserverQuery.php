<?php

namespace app\models\ActiveQuery;

use app\kernel\common\models\AQ\AQ;
use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\models\TaskObserver;
use yii\db\ActiveRecord;

class TaskObserverQuery extends AQ
{
	/**
	 * @return TaskObserver[]|ActiveRecord[]
	 */
	public function all($db = null): array
	{
		return parent::all($db);
	}

	/**
	 * @return TaskObserver|ActiveRecord|null
	 */
	public function one($db = null): ?TaskObserver
	{
		return parent::one($db);
	}

	/**
	 * @return TaskObserver|ActiveRecord|null
	 * @throws ModelNotFoundException
	 */
	public function oneOrThrow($db = null): TaskObserver
	{
		return parent::oneOrThrow($db);
	}

	public function notViewed(): self
	{
		return $this->andWhereNull($this->field('viewed_at'));
	}

	public function viewed(): self
	{
		return $this->andWhereNotNull($this->field('viewed_at'));
	}
}
