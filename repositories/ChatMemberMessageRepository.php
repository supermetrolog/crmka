<?php

declare(strict_types=1);

namespace app\repositories;

use app\models\ChatMemberMessage;
use app\models\ChatMemberMessageView;
use yii\db\BatchQueryResult;
use yii\db\Expression;

class ChatMemberMessageRepository
{
	/**
	 * @return BatchQueryResult
	 */
	public function findPreviousUnreadByMessage(ChatMemberMessage $message, int $from_chat_member_id): BatchQueryResult
	{
		$subQuery = ChatMemberMessageView::find()
		                                 ->andWhere([ChatMemberMessageView::field('chat_member_id') => $from_chat_member_id]);

		return ChatMemberMessage::find()
		                        ->with('notifications')
		                        ->leftJoin(['views' => $subQuery], [
			                        'views.chat_member_message_id' => new Expression(ChatMemberMessage::field('id')),
		                        ])
		                        ->andWhere(['<=', ChatMemberMessage::field('id'), $message->id])
		                        ->andWhere([ChatMemberMessage::field('to_chat_member_id') => $message->to_chat_member_id])
		                        ->andWhere(['views.chat_member_id' => null])
		                        ->notDeleted()
		                        ->each();
	}
}
