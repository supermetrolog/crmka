<?php

declare(strict_types=1);

namespace app\kernel\common\models;

use yii\db\ActiveQuery;

class AQ extends ActiveQuery
{
	protected function field(string $column): string
	{
		return $this->modelClass::tableName() . '.' . $column;
	}

	public function andWhereNull(string $column): self
	{
		return $this->andWhere([$column => null]);
	}

	public function andWhereNotNull(string $column): self
	{
		return $this->andWhere(['IS NOT', $column, null]);
	}
}