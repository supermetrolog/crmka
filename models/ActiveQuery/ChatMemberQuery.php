<?php

namespace app\models\ActiveQuery;

use app\kernel\common\models\AQ;
use app\models\ChatMember;
use yii\db\ActiveRecord;

/**
 * @see ChatMember
 */
class ChatMemberQuery extends AQ
{

    /**
     * @return ChatMember[]|ActiveRecord[]
     */
    public function all($db = null): array
    {
        return parent::all($db);
    }

	/**
	 * @return ChatMember|ActiveRecord|null
	 */
    public function one($db = null): ?ChatMember
    {
        return parent::one($db);
    }
}
