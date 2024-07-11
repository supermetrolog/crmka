<?php

namespace app\models\ActiveQuery;

use app\kernel\common\models\AQ\AQ;
use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\models\ChatMemberLastEvent;
use yii\db\ActiveRecord;

/**
 * This is the ActiveQuery class for [[ChatMemberLastEvent]].
 *
 * @see ChatMemberLastEvent
 */
class ChatMemberLastEventQuery extends AQ
{

    /**
     * @return ChatMemberLastEvent[]|ActiveRecord[]
     */
    public function all($db = null): array
    {
        return parent::all($db);
    }

	/**
	 * @return ChatMemberLastEvent|ActiveRecord|null
	 */
    public function one($db = null): ?ChatMemberLastEvent
    {
        return parent::one($db);
    }

	/**
	 * @return ChatMemberLastEvent|ActiveRecord
	 * @throws ModelNotFoundException
	 */
	public function oneOrThrow($db = null): ChatMemberLastEvent
	{
		return parent::oneOrThrow($db);
	}
}
