<?php

declare(strict_types=1);

namespace app\kernel\common\models;

use yii\db\ActiveQuery;
use yii\db\Connection;
use yii\db\Expression;

class AQ extends ActiveQuery
{
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
	 * @return $this
	 */
	public function andWhereColumn(string $first, string $second, string $operator = '='): self
	{
		return $this->andWhere([$operator, $first, new Expression($second)]);
	}
}