<?php

declare(strict_types=1);

namespace app\kernel\common\models;

use yii\db\ActiveQuery;

class AQ extends ActiveQuery
{
	public function field(string $column): string
	{
		return $this->modelClass::tableName() . '.' . $column;
	}
}