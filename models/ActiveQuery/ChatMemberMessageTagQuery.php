<?php

namespace app\models\ActiveQuery;

use app\kernel\common\models\AQ\AQ;
use app\kernel\common\models\AQ\SoftDeleteTrait;
use app\models\ChatMemberMessageTag;
use yii\db\ActiveRecord;

/**
 * This is the ActiveQuery class for [[\app\models\ChatMemberMessageTag]].
 *
 * @see ChatMemberMessageTag
 */
class ChatMemberMessageTagQuery extends AQ
{
    use SoftDeleteTrait;

	/**
	 * @return ChatMemberMessageTag[]|ActiveRecord[]
	 */
	public function all($db = null): array
	{
		return parent::all($db);
	}

	/**
	 * @return ChatMemberMessageTag|ActiveRecord|null
	 */
	public function one($db = null): ?ChatMemberMessageTag
	{
		return parent::one($db);
	}
}
