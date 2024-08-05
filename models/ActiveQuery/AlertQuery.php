<?php

namespace app\models\ActiveQuery;

use app\kernel\common\models\AQ\AQ;
use app\kernel\common\models\AQ\SoftDeleteTrait;
use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\models\Alert;
use yii\db\ActiveRecord;

/**
 * This is the ActiveQuery class for [[\app\models\Alert]].
 *
 * @see Alert
 */
class AlertQuery extends AQ
{
	use SoftDeleteTrait;

	/**
	 * @return Alert[]|ActiveRecord[]
	 */
	public function all($db = null): array
	{
		return parent::all($db);
	}

	/**
	 * @return Alert|ActiveRecord|null
	 */
	public function one($db = null): ?Alert
	{
		return parent::one($db);
	}

	/**
	 * @return Alert|ActiveRecord|null
	 * @throws ModelNotFoundException
	 */
	public function oneOrThrow($db = null): Alert
	{
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
}
