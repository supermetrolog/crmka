<?php

namespace app\models\ActiveQuery;

use app\kernel\common\models\AQ\AQ;
use app\models\ObjectChatMember;
use yii\db\ActiveRecord;

/**
 * @see ObjectChatMember
 */
class ObjectChatMemberQuery extends AQ
{

	/**
	 * @return ObjectChatMember[]|ActiveRecord[]
	 */
	public function all($db = null): array
	{
		return parent::all($db);
	}

	/**
	 * @return ObjectChatMember|ActiveRecord|null
	 */
	public function one($db = null): ?ObjectChatMember
	{
		return parent::one($db);
	}
}
