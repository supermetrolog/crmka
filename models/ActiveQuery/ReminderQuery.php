<?php

namespace app\models\ActiveQuery;

use app\kernel\common\models\AQ\AQ;
use app\kernel\common\models\AQ\SoftDeleteTrait;
use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\models\Reminder;
use yii\db\ActiveRecord;

class ReminderQuery extends AQ
{
	use SoftDeleteTrait;

	/**
	 * @return Reminder[]|ActiveRecord[]
	 */
	public function all($db = null): array
	{
		return parent::all($db);
	}

	/**
	 * @return Reminder|ActiveRecord|null
	 */
	public function one($db = null): ?Reminder
	{
		return parent::one($db);
	}

	/**
	 * @return Reminder|ActiveRecord|null
	 * @throws ModelNotFoundException
	 */
	public function oneOrThrow($db = null): Reminder
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
}
