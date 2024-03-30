<?php

namespace app\models\ActiveQuery;

use app\kernel\common\models\AQ;
use app\models\ChatMemberMessage;
use yii\db\ActiveRecord;

/**
 * This is the ActiveQuery class for [[\app\models\ChatMemberMessage]].
 *
 * @see \app\models\ChatMemberMessage
 */
class ChatMemberMessageQuery extends AQ
{

    /**
     * @return ChatMemberMessage[]|ActiveRecord[]
     */
    public function all($db = null): array
    {
        return parent::all($db);
    }

	/**
	 * @return ChatMemberMessage|ActiveRecord|null
	 */
    public function one($db = null): ?ChatMemberMessage
    {
        return parent::one($db);
    }
}
