<?php

namespace app\models\ActiveQuery;

use app\kernel\common\models\AQ;
use app\models\ChatMemberMessageTask;
use yii\db\ActiveRecord;

/**
 * This is the ActiveQuery class for [[\app\models\ChatMemberMessageTask]].
 *
 * @see \app\models\ChatMemberMessageTask
 */
class ChatMemberMessageTaskQuery extends AQ
{

    /**
     * @return ChatMemberMessageTask[]|ActiveRecord[]
     */
    public function all($db = null): array
    {
        return parent::all($db);
    }

	/**
	 * @return ChatMemberMessageTask|ActiveRecord|null
	 */
    public function one($db = null): ?ChatMemberMessageTask
    {
        return parent::one($db);
    }
}
