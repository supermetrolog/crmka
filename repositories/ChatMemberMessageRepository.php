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
	public function findPreviousUnreadByMessage(ChatMemberMessage $message): BatchQueryResult
	{
		return ChatMemberMessage::find()
		                        ->with('notifications')
		                        ->joinWith('views')
		                        ->andWhere(['<=', ChatMemberMessage::getColumn('id'), $message->id])
		                        ->andWhere([ChatMemberMessage::getColumn('to_chat_member_id') => $message->to_chat_member_id])
		                        ->andWhere(['OR',
		                                    ['IS', ChatMemberMessageView::getColumn('id'), null],
		                                    ['!=', ChatMemberMessageView::getColumn('chat_member_id'), new Expression(ChatMemberMessage::getColumn('from_chat_member_id'))],
		                        ])
		                        ->notDeleted()
		                        ->each();
	}
}
