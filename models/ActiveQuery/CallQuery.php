<?php

namespace app\models\ActiveQuery;

use app\kernel\common\models\AQ\AQ;
use app\kernel\common\models\exceptions\ModelNotFoundException;
use yii\db\ActiveRecord;
use app\models\Call;

/**
 * This is the ActiveQuery class for [[Call]].
 *
 * @see Call
 */
class CallQuery extends AQ
{

    /**
     * @return Call[]|ActiveRecord[]
     */
    public function all($db = null): array
    {
        return parent::all($db);
    }

	/**
	 * @return Call|ActiveRecord|null
	 */
    public function one($db = null): ?Call
    {
        return parent::one($db);
    }

	/**
	 * @return Call|ActiveRecord
	 * @throws ModelNotFoundException
	 */
	public function oneOrThrow($db = null): Call
	{
		return parent::oneOrThrow($db);
	}
}
