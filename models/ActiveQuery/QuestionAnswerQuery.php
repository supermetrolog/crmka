<?php

namespace app\models\ActiveQuery;

use app\kernel\common\models\AQ\AQ;
use app\kernel\common\models\AQ\SoftDeleteTrait;
use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\models\QuestionAnswer;
use yii\db\ActiveRecord;

/**
 * This is the ActiveQuery class for [[QuestionAnswer]].
 *
 * @see QuestionAnswer
 */
class QuestionAnswerQuery extends AQ
{
	use SoftDeleteTrait;

    /**
     * @return QuestionAnswer[]|ActiveRecord[]
     */
    public function all($db = null): array
    {
        return parent::all($db);
    }

	/**
	 * @return QuestionAnswer|ActiveRecord|null
	 */
    public function one($db = null): ?QuestionAnswer
    {
        return parent::one($db);
    }

	/**
	 * @return QuestionAnswer|ActiveRecord
	 * @throws ModelNotFoundException
	 */
	public function oneOrThrow($db = null): QuestionAnswer
	{
		return parent::oneOrThrow($db);
	}
}
