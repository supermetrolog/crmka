<?php

namespace app\models\ActiveQuery;

use app\kernel\common\models\AQ\AQ;
use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\models\Field;
use yii\db\ActiveRecord;

/**
 * This is the ActiveQuery class for [[\app\models\Field]].
 *
 * @see Field
 */
class FieldQuery extends AQ
{

    /**
     * @return Field[]|ActiveRecord[]
     */
    public function all($db = null): array
    {
        return parent::all($db);
    }

	/**
	 * @return Field|ActiveRecord|null
	 */
    public function one($db = null): ?Field
    {
        return parent::one($db);
    }

	/**
	 * @return Field|ActiveRecord
	 * @throws ModelNotFoundException
	 */
	public function oneOrThrow($db = null): Field
	{
		return parent::oneOrThrow($db);
	}
}
