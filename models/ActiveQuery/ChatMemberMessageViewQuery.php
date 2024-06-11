<?php

namespace app\models\ActiveQuery;

use app\kernel\common\models\AQ\AQ;
use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\models\ChatMemberMessageView;
use yii\db\ActiveRecord;

/**
 * This is the ActiveQuery class for [[ChatMemberMessageView]].
 *
 * @see ChatMemberMessageView
 */
class ChatMemberMessageViewQuery extends AQ
{
	/**
	 * @return ChatMemberMessageView[]|ActiveRecord[]
	 */
	public function all($db = null): array
	{
		return parent::all($db);
	}

	/**
	 * @return ChatMemberMessageView|ActiveRecord|null
	 */
	public function one($db = null): ?ChatMemberMessageView
	{
		return parent::one($db);
	}

	/**
	 * @return ChatMemberMessageView|ActiveRecord
	 * @throws ModelNotFoundException
	 */
	public function oneOrThrow($db = null): ChatMemberMessageView
	{
		return parent::oneOrThrow($db);
	}
}
