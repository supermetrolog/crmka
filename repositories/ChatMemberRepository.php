<?php

declare(strict_types=1);

namespace app\repositories;

use app\helpers\ArrayHelper;
use app\kernel\common\models\AQ\AQ;
use app\models\ChatMember;
use app\models\ChatMemberLastEvent;
use app\models\ChatMemberMessage;
use app\models\ChatMemberMessageView;
use app\models\Notification\UserNotification;
use app\models\Relation;
use app\models\Task;
use app\models\TaskObserver;
use app\models\views\ChatMemberStatisticView;
use yii\base\ErrorException;
use yii\db\Expression;

class ChatMemberRepository
{
	/**
	 * @throws ErrorException
	 */
	public function getStatisticByIdsAndModelTypes($chat_member_ids, $model_types): array
	{
		$model_types     = ArrayHelper::isArray($model_types) ? $model_types : [$model_types];
		$chat_member_ids = ArrayHelper::isArray($chat_member_ids) ? $chat_member_ids : [(int)$chat_member_ids];

		$lastEventQuery = ChatMemberLastEvent::find()
		                                     ->select([
			                                     ChatMemberLastEvent::field('event_chat_member_id')
		                                     ])
		                                     ->andWhere([
			                                     ChatMemberLastEvent::field('chat_member_id') => new Expression(ChatMemberStatisticView::field('id'))
		                                     ]);

		$chatMemberQuery = ChatMemberStatisticView::find()
		                                          ->select([
			                                          'chat_member_id'            => ChatMemberStatisticView::field('id'),
			                                          'unread_task_count'         => 'COUNT(DISTINCT tasks.id)',
			                                          'unread_notification_count' => 'COUNT(DISTINCT notifications.id)',
			                                          'unread_message_count'      => 'COUNT(DISTINCT messages.id) - COUNT(DISTINCT message_views.id)',
			                                          'outdated_call_count'       => $this->makeNeedingCallCountQuery($model_types)
		                                          ])
		                                          ->leftJoin(['tasks' => $this->makeTaskQuery($model_types)], [
			                                          'tasks.observer_id' => new Expression(ChatMemberStatisticView::field('model_id'))
		                                          ])
		                                          ->leftJoin(['notifications' => $this->makeNotificationQuery($model_types)], [
			                                          'notifications.user_id' => new Expression(ChatMemberStatisticView::field('model_id'))
		                                          ])
		                                          ->leftJoin(['messages' => $this->makeMessagesQuery($model_types)], [
			                                          'messages.to_chat_member_id' => $lastEventQuery,
		                                          ])
		                                          ->leftJoin(['message_views' => ChatMemberMessageView::getTable()], [
			                                          'and',
			                                          ['message_views.chat_member_message_id' => new Expression('messages.id')],
			                                          [
				                                          'or',
				                                          ['message_views.chat_member_id' => new Expression(ChatMemberStatisticView::field('id'))],
				                                          ['message_views.chat_member_id' => null],
			                                          ]
		                                          ])
		                                          ->andWhere([ChatMemberStatisticView::field('id') => $chat_member_ids])
		                                          ->groupBy(ChatMemberStatisticView::field('id'));

		return $chatMemberQuery->all();
	}

	/**
	 * @throws ErrorException
	 */
	private function makeTaskQuery($model_types): AQ
	{
		return Task::find()
		           ->select([
			           'id'          => Task::field('id'),
			           'user_id'     => Task::field('user_id'),
			           'viewed_at'   => 'to.viewed_at',
			           'observer_id' => 'to.user_id'
		           ])
		           ->leftJoin(Relation::getTable(), [
			           Relation::field('first_type')  => ChatMemberMessage::getMorphClass(),
			           Relation::field('second_type') => Task::getMorphClass(),
			           Relation::field('second_id')   => new Expression(Task::field('id')),
		           ])
		           ->leftJoin(ChatMemberMessage::getTable(), [
			           ChatMemberMessage::field('id') => new Expression(Relation::field('first_id')),
		           ])
		           ->leftJoin(['to' => TaskObserver::getTable()], [
			           'to.task_id' => new Expression(Task::field('id'))
		           ])
		           ->leftJoin(['chat_member' => ChatMember::getTable()], [
			           'chat_member.id' => new Expression(ChatMemberMessage::field('to_chat_member_id'))
		           ])
		           ->andWhereNotNull(ChatMemberMessage::field('id'))
		           ->andWhere([
			           'to.viewed_at'           => null,
			           'to.task_id'             => new Expression(Task::field('id')),
			           'chat_member.model_type' => $model_types
		           ])
		           ->notDeleted();
	}

	/**
	 * @throws ErrorException
	 */
	private function makeNotificationQuery($model_types): AQ
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
		                       ->leftJoin(['chat_member' => ChatMember::getTable()], [
			                       'chat_member.id' => new Expression(ChatMemberMessage::field('to_chat_member_id'))
		                       ])
		                       ->andWhereNull(UserNotification::field('viewed_at'))
		                       ->andWhereNotNull(ChatMemberMessage::field('id'))
		                       ->andWhere([
			                       'chat_member.model_type' => $model_types
		                       ]);
	}

	/**
	 * @throws ErrorException
	 */
	private function makeNeedingCallCountQuery($model_types): AQ
	{
		return ChatMember::find()
		                 ->select([
			                 'count' => 'COUNT(*)'
		                 ])
		                 ->leftJoinLastCallRelation()
		                 ->byModelTypes($model_types)
		                 ->joinWith(['objectChatMember.object', 'request'])
		                 ->needCalling();
	}

	/**
	 * @throws ErrorException
	 */
	private function makeMessagesQuery($model_types): AQ
	{
		return ChatMemberMessage::find()
		                        ->select([
			                        'id'                => ChatMemberMessage::field('id'),
			                        'to_chat_member_id' => ChatMemberMessage::field('to_chat_member_id')
		                        ])
		                        ->leftJoin(['chat_member_rel' => ChatMember::getTable()], [
			                        'chat_member_rel.id' => new Expression(ChatMemberMessage::field('to_chat_member_id'))
		                        ])
		                        ->andWhere(['chat_member_rel.model_type' => $model_types])
		                        ->notDeleted();
	}
}
