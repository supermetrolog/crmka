<?php

namespace app\models\ActiveQuery;

use app\kernel\common\models\AQ\AQ;
use app\models\ChatMemberMessage;
use yii\db\ActiveRecord;

/**
 * @see ChatMemberMessage
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

	public function byFromChatMemberId(int $id): self
	{
		return $this->andWhere([$this->field('from_chat_member_id') => $id]);
	}

	public function byToChatMemberId(int $id): self
	{
		return $this->andWhere([$this->field('to_chat_member_id') => $id]);
	}

	public function notDeleted(): self
	{
		return $this->andWhereNull($this->field('deleted_at'));
	}

	public function byTemplate(string $template): self
	{
		return $this->andWhere([$this->field('template') => $template]);
	}
}
