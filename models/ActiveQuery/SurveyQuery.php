<?php

namespace app\models\ActiveQuery;

use app\kernel\common\models\AQ\AQ;
use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\models\Survey;
use yii\db\ActiveRecord;

/**
 * This is the ActiveQuery class for [[\app\models\Surveys]].
 *
 * @see Survey
 */
class SurveyQuery extends AQ
{
    /**
     * @return Survey[]|ActiveRecord[]
     */
    public function all($db = null): array
    {
        return parent::all($db);
    }

	/**
	 * @return Survey|ActiveRecord|null
	 */
    public function one($db = null): ?Survey
    {
        return parent::one($db);
    }

	/**
	 * @return Survey|ActiveRecord
	 * @throws ModelNotFoundException
	 */
	public function oneOrThrow($db = null): Survey
	{
		return parent::oneOrThrow($db);
	}
}
