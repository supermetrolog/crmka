<?php

namespace app\models\ActiveQuery;

use app\kernel\common\models\AQ\AQ;
use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\models\Surveys;
use yii\db\ActiveRecord;

/**
 * This is the ActiveQuery class for [[\app\models\Surveys]].
 *
 * @see Surveys
 */
class SurveysQuery extends AQ
{
    /**
     * @return Surveys[]|ActiveRecord[]
     */
    public function all($db = null): array
    {
        return parent::all($db);
    }

	/**
	 * @return Surveys|ActiveRecord|null
	 */
    public function one($db = null): ?Surveys
    {
        return parent::one($db);
    }

	/**
	 * @return Surveys|ActiveRecord
	 * @throws ModelNotFoundException
	 */
	public function oneOrThrow($db = null): Surveys
	{
		return parent::oneOrThrow($db);
	}
}
