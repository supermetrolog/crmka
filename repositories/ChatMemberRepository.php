<?php

declare(strict_types=1);

namespace app\repositories;

use app\kernel\common\models\AQ\AQ;
use app\models\ChatMember;
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

		$messageSubQuery = ChatMemberMessage::find()
		                                    ->select(ChatMemberMessage::field('to_chat_member_id'))
		                                    ->andWhere([
			                                    ChatMemberMessage::field('from_chat_member_id') => new Expression(ChatMemberSearchView::field('id')),
		                                    ])
		                                    ->notDeleted()
		                                    ->groupBy(ChatMemberMessage::field('to_chat_member_id'));

		$chatMemberQuery = ChatMemberSearchView::find()
		                                       ->select([
			                                       'chat_member_id'            => ChatMemberSearchView::field('id'),
			                                       'unread_task_count'         => 'COUNT(DISTINCT tasks.id)',
			                                       'unread_reminder_count'     => 'COUNT(DISTINCT reminders.id)',
			                                       'unread_notification_count' => 'COUNT(DISTINCT notifications.id)',
			                                       'unread_message_count'      => 'COUNT(DISTINCT messages.id)',
		                                       ])
		                                       ->leftJoin(['tasks' => $this->makeTaskQuery()], [
			                                       'tasks.from_chat_member_id' => new Expression(ChatMemberSearchView::field('id'))
		                                       ])
		                                       ->leftJoin(['reminders' => $this->makeReminderQuery()], [
			                                       'reminders.from_chat_member_id' => new Expression(ChatMemberSearchView::field('id'))
		                                       ])
		                                       ->leftJoin(['notifications' => $this->makeNotificationQuery()], [
			                                       'notifications.from_chat_member_id' => new Expression(ChatMemberSearchView::field('id'))
		                                       ])
		                                       ->leftJoin(['messages' => $this->makeMessageQuery()], [
			                                       'and',
			                                       ['messages.to_chat_member_id' => $messageSubQuery],
			                                       [
				                                       'or',
				                                       ['!=', 'messages.from_chat_member_id', new Expression(ChatMemberSearchView::field('id'))],
				                                       ['messages.from_chat_member_id' => null],
			                                       ]
		                                       ])
		                                       ->andWhere([ChatMemberSearchView::field('id') => $chat_member_ids])
		                                       ->groupBy(ChatMemberSearchView::field('id'));

		return $chatMemberQuery->asArray()->all();
	}

	private function makeTaskQuery(): AQ
	{
		$relationQuery = Relation::find()
		                         ->byFirstType(ChatMemberMessage::getMorphClass())
		                         ->bySecondType(Task::getMorphClass());

		return Task::find()
		           ->select([
			           'id'                  => Task::field('id'),
			           'from_chat_member_id' => ChatMemberMessage::field('from_chat_member_id'),
		           ])
		           ->leftJoin(Relation::getTable(), [
			           Relation::field('second_id') => new Expression(Task::field('id')),
		           ])
		           ->leftJoin(ChatMemberMessage::getTable(), [
			           ChatMemberMessage::field('id') => new Expression(Relation::field('first_id')),
		           ])
		           ->notCompleted()
		           ->notDeleted()
		           ->andWhereNotNull(ChatMemberMessage::field('id'));
	}

	private function makeReminderQuery(): AQ
	{
		$relationQuery = Relation::find()
		                         ->byFirstType(ChatMemberMessage::getMorphClass())
		                         ->bySecondType(Reminder::getMorphClass());

		return Reminder::find()
		               ->select([
			               'id'                  => Reminder::field('id'),
			               'from_chat_member_id' => ChatMemberMessage::field('from_chat_member_id'),
		               ])
		               ->leftJoin(Relation::getTable(), [
			               Relation::field('second_id') => new Expression(Reminder::field('id')),
		               ])
		               ->leftJoin(ChatMemberMessage::getTable(), [
			               ChatMemberMessage::field('id') => new Expression(Relation::field('first_id')),
		               ])
		               ->notNotified()
		               ->notDeleted()
		               ->andWhereNotNull(ChatMemberMessage::field('id'));
	}

	private function makeNotificationQuery(): AQ
	{
		$relationQuery = Relation::find()
		                         ->byFirstType(ChatMemberMessage::getMorphClass())
		                         ->bySecondType(UserNotification::getMorphClass());

		return UserNotification::find()
		                       ->select([
			                       'id'                  => UserNotification::field('id'),
			                       'from_chat_member_id' => ChatMemberMessage::field('from_chat_member_id'),
		                       ])
		                       ->leftJoin(Relation::getTable(), [
			                       Relation::field('second_id') => new Expression(UserNotification::field('id')),
		                       ])
		                       ->leftJoin(ChatMemberMessage::getTable(), [
			                       ChatMemberMessage::field('id') => new Expression(Relation::field('first_id')),
		                       ])
		                       ->andWhereNull(UserNotification::field('viewed_at'))
		                       ->andWhereNotNull(ChatMemberMessage::field('id'));
	}

	private function makeMessageQuery(): AQ
	{
		return ChatMemberMessage::find()
		                        ->select([
			                        'id'                  => ChatMemberMessage::field('id'),
			                        'to_chat_member_id'   => ChatMemberMessage::field('to_chat_member_id'),
			                        'from_chat_member_id' => ChatMemberMessageView::field('chat_member_id'),
		                        ])
		                        ->joinWith('views')
		                        ->notDeleted();
	}
}
