<?php

declare(strict_types=1);

namespace app\models\ActiveQuery;

use app\models\Block;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

class BlockQuery extends ActiveQuery
{

	/**
	 * @param $db
	 *
	 * @return ActiveRecord|BLock|array|null
	 */
	public function one($db = null): ?Block
	{
		return parent::one($db);
	}

	/**
	 * @param $db
	 *
	 * @return array|ActiveRecord[]|Block[]
	 */
	public function all($db = null): array
	{
		return parent::all($db);
	}

	public function active(): self
	{
		return $this->andWhere([
			'status'  => 1,
			'deleted' => 0
		]);
	}
}