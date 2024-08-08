<?php

declare(strict_types=1);

namespace app\repositories;

use app\kernel\common\models\AQ\AQ;
use app\models\ChatMember;
use app\models\ChatMemberLastEvent;
use app\models\ChatMemberMessage;
use app\models\ChatMemberMessageView;
use app\models\Notification\UserNotification;
use app\models\Relation;
use app\models\Reminder;
use app\models\Task;
use app\models\views\ChatMemberSearchView;
use yii\db\Expression;

class ChatMemberRepository
{
	public function getStatisticByIds($chat_member_ids): array
	{
		$chat_member_ids = is_array($chat_member_ids) ? $chat_member_ids : [(int)$chat_member_ids];

		$lastEventQuery = ChatMemberLastEvent::find()
		                                     ->select([
			                                     ChatMemberLastEvent::field('event_chat_member_id')
		                                     ])
		                                     ->andWhere([
												 ChatMemberLastEvent::field('chat_member_id') => new Expression(ChatMemberSearchView::field('id'))
		                                     ]);

		$chatMemberQuery = ChatMemberSearchView::find()
		                                       ->select([
			                                       'chat_member_id'            => ChatMemberSearchView::field('id'),
			                                       'unread_task_count'         => 'COUNT(DISTINCT tasks.id)',
			                                       'unread_reminder_count'     => 'COUNT(DISTINCT reminders.id)',
			                                       'unread_notification_count' => 'COUNT(DISTINCT notifications.id)',
			                                       'unread_message_count'      => 'COUNT(DISTINCT messages.id) - COUNT(DISTINCT message_views.id)',
		                                       ])
		                                       ->leftJoin(['tasks' => $this->makeTaskQuery()], [
			                                       'tasks.user_id' => new Expression(ChatMemberSearchView::field('model_id'))
		                                       ])
		                                       ->leftJoin(['reminders' => $this->makeReminderQuery()], [
			                                       'reminders.user_id' => new Expression(ChatMemberSearchView::field('model_id'))
		                                       ])
		                                       ->leftJoin(['notifications' => $this->makeNotificationQuery()], [
			                                       'notifications.user_id' => new Expression(ChatMemberSearchView::field('model_id'))
		                                       ])
		                                       ->leftJoin(['messages' => ChatMemberMessage::find()->notDeleted()], [
			                                       'messages.to_chat_member_id' => $lastEventQuery,
		                                       ])
		                                       ->leftJoin(['message_views' => ChatMemberMessageView::getTable()], [
			                                       'and',
			                                       ['message_views.chat_member_message_id' => new Expression('messages.id')],
			                                       [
				                                       'or',
				                                       ['message_views.chat_member_id' => new Expression(ChatMemberSearchView::field('id'))],
				                                       ['message_views.chat_member_id' => null],
			                                       ]
		                                       ])
		                                       ->andWhere([ChatMemberSearchView::field('id') => $chat_member_ids])
		                                       ->groupBy(ChatMemberSearchView::field('id'));

		return $chatMemberQuery->asArray()->all();
	}

	private function makeTaskQuery(): AQ
	{
		return Task::find()
		           ->select([
			           'id'      => Task::field('id'),
			           'user_id' => Task::field('user_id'),
		           ])
		           ->leftJoin(Relation::getTable(), [
			           Relation::field('first_type')  => ChatMemberMessage::getMorphClass(),
			           Relation::field('second_type') => Task::getMorphClass(),
			           Relation::field('second_id')   => new Expression(Task::field('id')),
		           ])
		           ->leftJoin(ChatMemberMessage::getTable(), [
			           ChatMemberMessage::field('id') => new Expression(Relation::field('first_id')),
		           ])
		           ->andWhereNotNull(ChatMemberMessage::field('id'))
		           ->notCompleted()
		           ->notImpossible()
		           ->notDeleted();
	}

	private function makeReminderQuery(): AQ
	{
		return Reminder::find()
		               ->select([
			               'id'      => Reminder::field('id'),
			               'user_id' => Reminder::field('user_id'),
		               ])
		               ->leftJoin(Relation::getTable(), [
			               Relation::field('first_type')  => ChatMemberMessage::getMorphClass(),
			               Relation::field('second_type') => Reminder::getMorphClass(),
			               Relation::field('second_id')   => new Expression(Reminder::field('id')),
		               ])
		               ->leftJoin(ChatMemberMessage::getTable(), [
			               ChatMemberMessage::field('id') => new Expression(Relation::field('first_id')),
		               ])
		               ->andWhereNotNull(ChatMemberMessage::field('id'))
		               ->notNotified()
		               ->notDeleted();
	}

	private function makeNotificationQuery(): AQ
	{
		return UserNotification::find()
		                       ->select([
			                       'id'      => UserNotification::field('id'),
			                       'user_id' => UserNotification::field('user_id'),
		                       ])
		                       ->leftJoin(Relation::getTable(), [
			                       Relation::field('first_type')  => ChatMemberMessage::getMorphClass(),
			                       Relation::field('second_type') => UserNotification::getMorphClass(),
			                       Relation::field('second_id')   => new Expression(UserNotification::field('id')),
		                       ])
		                       ->leftJoin(ChatMemberMessage::getTable(), [
			                       ChatMemberMessage::field('id') => new Expression(Relation::field('first_id')),
		                       ])
		                       ->andWhereNull(UserNotification::field('viewed_at'))
		                       ->andWhereNotNull(ChatMemberMessage::field('id'));
	}
}
