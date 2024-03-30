<?php

namespace app\models\ActiveQuery;

use app\kernel\common\models\AQ;
use app\models\Task;
use yii\db\ActiveRecord;

/**
 * This is the ActiveQuery class for [[\app\models\Task]].
 *
 * @see \app\models\Task
 */
class TaskQuery extends AQ
{

    /**
     * @return Task[]|ActiveRecord[]
     */
    public function all($db = null): array
    {
        return parent::all($db);
    }

	/**
	 * @return Task|ActiveRecord|null
	 */
    public function one($db = null): ?Task
    {
        return parent::one($db);
    }
}
