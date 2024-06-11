<?php

declare(strict_types=1);

namespace app\repositories;

use app\models\ChatMemberMessage;
use app\models\ChatMemberMessageView;
use yii\db\ActiveRecord;
use yii\db\conditions\OrCondition;

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
		                        ->andWhere(new OrCondition([
			                        [ChatMemberMessageView::getColumn('id') => null],
			                        ChatMemberMessageView::getColumn('chat_member_id') . '!=' . ChatMemberMessage::getColumn('from_chat_member_id'),
		                        ]))
		                        ->notDeleted()
		                        ->all();
	}
}
