<?php

declare(strict_types=1);

namespace app\repositories;

use app\models\ChatMemberMessage;
use app\models\ChatMemberMessageView;
use yii\db\ActiveRecord;

class ChatMemberMessageRepository
{
	/**
	 * @return ChatMemberMessage[]|ActiveRecord[]
	 */
	public function findPreviousUnreadByMessage(ChatMemberMessage $message): array
	{
		return ChatMemberMessage::find()
		                        ->leftJoin(ChatMemberMessageView::getTable(), ChatMemberMessageView::getColumn('chat_member_message_id') . '=' . ChatMemberMessage::getColumn('id'))
		                        ->where(['<=', ChatMemberMessage::getColumn('id'), $message->id])
		                        ->andWhere([ChatMemberMessage::getColumn('to_chat_member_id') => $message->to_chat_member_id])
		                        ->andWhereNull(ChatMemberMessageView::getColumn('id'))
		                        ->notDeleted()
		                        ->all();
	}
}
