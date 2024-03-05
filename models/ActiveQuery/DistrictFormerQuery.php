<?php

namespace app\models\ActiveQuery;

use app\models\DistrictFormer;
use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[DistrictFormer]].
 *
 * @see DistrictFormer
 */
class DistrictFormerQuery extends ActiveQuery
{
	/**
	 * @param $db
	 *
	 * @return DistrictFormer[]
	 */
	public function all($db = null): array
	{
		return parent::all($db);
	}

	/**
	 * @return DistrictFormer|array|null
	 */
	public function one($db = null): ?DistrictFormer
	{
		return parent::one($db);
	}
}
