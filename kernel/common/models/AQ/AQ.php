<?php

declare(strict_types=1);

namespace app\kernel\common\models\AQ;

use app\kernel\common\models\AR\AR;
use app\kernel\common\models\exceptions\ModelNotFoundException;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Connection;
use yii\db\Expression;

class AQ extends ActiveQuery
{
	/**
	 * @var ActiveRecord|AR
	 */
	public $modelClass;

	protected function field(string $column): string
	{
		return $this->getPrimaryTableName() . '.' . $column;
	}

	/**
	 * @param string $column
	 *
	 * @return $this
	 */
	public function andWhereNull(string $column): self
	{
		return $this->andWhere([$column => null]);
	}

	/**
	 * @param string $column
	 *
	 * @return $this
	 */
	public function andWhereNotNull(string $column): self
	{
		return $this->andWhere(['IS NOT', $column, null]);
	}

	public function getRawSql(?Connection $db = null): string
	{
		return $this->createCommand($db)->getRawSql();
	}

	public function getSql(?Connection $db = null): string
	{
		return $this->createCommand($db)->getSql();
	}

	/**
	 * @param int $id
	 *
	 * @return $this
	 */
	public function byId(int $id): self
	{
		return $this->andWhere([$this->field('id') => $id]);
	}


	/**
	 * @param array $id
	 *
	 * @return $this
	 */
	public function byIds(array $id): self
	{
		return $this->andWhere([$this->field('id') => $id]);
	}

	/**
	 * @return $this
	 */
	public function andWhereColumn(string $first, string $second, string $operator = '='): self
	{
		return $this->andWhere([$operator, $first, new Expression($second)]);
	}

	/**
	 * @return $this
	 */
	public function andWhereExpr(string $first, string $second, string $operator = '=', array $params = []): self
	{
		return $this->andWhere([$operator, $first, new Expression($second, $params)]);
	}


	/**
	 * @return ActiveRecord|array
	 * @throws ModelNotFoundException
	 */
	public function oneOrThrow($db = null): ActiveRecord
	{
		$model = $this->one($db);

		if ($model) {
			return $model;
		}

		throw new ModelNotFoundException();
	}

	/**
	 * @return $this
	 */
	public function andWhereNotIn(string $column, array $value): self
	{
		return $this->andWhere(['NOT IN', $column, $value]);
	}
}