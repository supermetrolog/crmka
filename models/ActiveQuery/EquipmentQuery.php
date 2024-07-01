<?php

namespace app\models\ActiveQuery;

use app\kernel\common\models\AQ\AQ;
use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\models\Equipment;
use yii\db\ActiveRecord;

/**
 * This is the ActiveQuery class for [[\app\models\Equipment]].
 *
 * @see Equipment
 */
class EquipmentQuery extends AQ
{

    /**
     * @return Equipment[]|ActiveRecord[]
     */
    public function all($db = null): array
    {
        return parent::all($db);
    }

	/**
	 * @return Equipment|ActiveRecord|null
	 */
    public function one($db = null): ?Equipment
    {
        return parent::one($db);
    }

	/**
	 * @return Equipment|ActiveRecord
	 * @throws ModelNotFoundException
	 */
	public function oneOrThrow($db = null): Equipment
	{
		return parent::oneOrThrow($db);
	}
}
