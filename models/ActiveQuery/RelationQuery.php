<?php

namespace app\models\ActiveQuery;

use app\kernel\common\models\AQ\AQ;
use app\models\Relation;
use yii\db\ActiveRecord;

/**
 * This is the ActiveQuery class for [[\app\models\Relation]].
 *
 * @see Relation
 */
class RelationQuery extends AQ
{

	/**
	 * @return Relation[]|ActiveRecord[]
	 */
	public function all($db = null): array
	{
		return parent::all($db);
	}

	/**
	 * @return Relation|ActiveRecord|null
	 */
	public function one($db = null): ?Relation
	{
		return parent::one($db);
	}

	public function byFirst(int $id, string $type): self
	{
		return $this->andWhere([
			$this->field('first_id')   => $id,
			$this->field('first_type') => $type
		]);
	}

	public function bySecond(int $id, string $type): self
	{
		return $this->andWhere([
			$this->field('second_id')   => $id,
			$this->field('second_type') => $type
		]);
	}


	public function bySecondType(string $type): self
	{
		return $this->andWhere([
			$this->field('second_type') => $type
		]);
	}

	public function notSecondIds(array $ids): self
	{
		return $this->andWhereNotIn($this->field('second_id'), $ids);
	}
}
