<?php

declare(strict_types=1);

namespace app\repositories;

use app\models\ActiveQuery\ChatMemberMessageQuery;
use app\models\ActiveQuery\ChatMemberQuery;
use app\models\ActiveQuery\TaskQuery;
use app\models\ActiveQuery\UserNotificationQuery;
use app\models\ChatMember;
use app\models\ChatMemberLastEvent;
use app\models\ChatMemberMessage;
use app\models\ChatMemberMessageView;
use app\models\Notification\UserNotification;
use app\models\Objects;
use app\models\Relation;
use app\models\Request;
use app\models\Task;
use app\models\TaskObserver;
use app\models\User;
use app\models\views\ChatMemberStatisticView;
use yii\base\ErrorException;
use yii\db\Expression;

class ChatMemberRepository
{
	/**
	 * @param int[]    $chat_member_ids
	 * @param string[] $model_types array of model types like ['request', 'object', 'user] etc. (use getMorphClass() method in models)
	 *
	 * @return ChatMemberStatisticView[]
	 * @throws ErrorException
	 */
	public function getStatisticByIdsAndModelTypes(array $chat_member_ids, array $model_types): array
	{
		$outdated_call_count = $this->makeNeedingCallCountQuery($model_types, $chat_member_ids);

		$lastEventQuery = ChatMemberLastEvent::find()
		                                     ->select([
			                                     ChatMemberLastEvent::field('event_chat_member_id')
		                                     ])
		                                     ->andWhere([
			                                     ChatMemberLastEvent::field('chat_member_id') => ChatMemberStatisticView::xfield('id')
		                                     ]);

		$chatMemberQuery = ChatMemberStatisticView::find()
		                                          ->select([
			                                          'chat_member_id'            => ChatMemberStatisticView::field('id'),
			                                          'unread_task_count'         => 'COUNT(DISTINCT tasks.id)',
			                                          'unread_notification_count' => 'COUNT(DISTINCT notifications.id)',
			                                          'unread_message_count'      => 'COUNT(DISTINCT messages.id) - COUNT(DISTINCT message_views.id)',
			                                          'outdated_call_count'       => $outdated_call_count
		                                          ])
		                                          ->leftJoin(['tasks' => $this->makeTaskQuery($model_types)], [
			                                          'tasks.observer_id' => ChatMemberStatisticView::xfield('model_id')
		                                          ])
		                                          ->leftJoin(['notifications' => $this->makeNotificationQuery($model_types)], [
			                                          'notifications.user_id' => ChatMemberStatisticView::xfield('model_id')
		                                          ])
		                                          ->leftJoin(['messages' => $this->makeMessagesQuery($model_types)], [
			                                          'messages.to_chat_member_id' => $lastEventQuery,
		                                          ])
		                                          ->leftJoin(['message_views' => ChatMemberMessageView::getTable()], [
			                                          'and',
			                                          ['message_views.chat_member_message_id' => new Expression('messages.id')],
			                                          [
				                                          'or',
				                                          ['message_views.chat_member_id' => ChatMemberStatisticView::xfield('id')],
				                                          ['message_views.chat_member_id' => null],
			                                          ]
		                                          ])
		                                          ->andWhere([ChatMemberStatisticView::field('id') => $chat_member_ids])
		                                          ->groupBy(ChatMemberStatisticView::field('id'));

		return $chatMemberQuery->all();
	}

	/**
	 * @param string[] $model_types
	 *
	 * @return TaskQuery
	 * @throws ErrorException
	 */
	private function makeTaskQuery(array $model_types): TaskQuery
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
			           Relation::field('second_id')   => Task::xfield('id'),
		           ])
		           ->leftJoin(ChatMemberMessage::getTable(), [
			           ChatMemberMessage::field('id') => Relation::xfield('first_id'),
		           ])
		           ->leftJoin(['to' => TaskObserver::getTable()], [
			           'to.task_id' => Task::xfield('id')
		           ])
		           ->leftJoin(['chat_member' => ChatMember::getTable()], [
			           'chat_member.id' => ChatMemberMessage::xfield('to_chat_member_id')
		           ])
		           ->andWhereNotNull(ChatMemberMessage::field('id'))
		           ->andWhere([
			           'to.viewed_at'           => null,
			           'to.task_id'             => Task::xfield('id'),
			           'chat_member.model_type' => $model_types
		           ])
		           ->notDeleted();
	}

	/**
	 * @param string[] $model_types
	 *
	 * @return UserNotificationQuery
	 * @throws ErrorException
	 */
	private function makeNotificationQuery(array $model_types): UserNotificationQuery
	{
		return UserNotification::find()
		                       ->select([
			                       'id'      => UserNotification::field('id'),
			                       'user_id' => UserNotification::field('user_id'),
		                       ])
		                       ->leftJoin(Relation::getTable(), [
			                       Relation::field('first_type')  => ChatMemberMessage::getMorphClass(),
			                       Relation::field('second_type') => UserNotification::getMorphClass(),
			                       Relation::field('second_id')   => UserNotification::xfield('id'),
		                       ])
		                       ->leftJoin(ChatMemberMessage::getTable(), [
			                       ChatMemberMessage::field('id') => Relation::xfield('first_id'),
		                       ])
		                       ->leftJoin(['chat_member' => ChatMember::getTable()], [
			                       'chat_member.id' => ChatMemberMessage::xfield('to_chat_member_id')
		                       ])
		                       ->andWhereNull(UserNotification::field('viewed_at'))
		                       ->andWhereNotNull(ChatMemberMessage::field('id'))
		                       ->andWhere([
			                       'chat_member.model_type' => $model_types
		                       ]);
	}

	/**
	 * @param string[] $model_types
	 * @param int[]    $chat_member_ids
	 *
	 * @return ChatMemberQuery
	 * @throws ErrorException
	 */
	private function makeNeedingCallCountQuery(array $model_types, array $chat_member_ids): ChatMemberQuery
	{
		$chat_member_models_ids = ChatMember::find()
		                                    ->select([
			                                    'id' => ChatMember::field('model_id')
		                                    ])
		                                    ->andWhere([ChatMember::field('id') => $chat_member_ids])
		                                    ->column();

		$old_users_ids = User::find()
		                     ->select([
			                     'id' => User::field('user_id_old')
		                     ])
		                     ->andWhere([User::field('id') => $chat_member_models_ids])
		                     ->column();


		return ChatMember::find()
		                 ->select([
			                 'count' => 'COUNT(*)'
		                 ])
		                 ->leftJoinLastCallRelation()
		                 ->byModelTypes($model_types)
		                 ->joinWith(['objectChatMember.object', 'request'])
		                 ->needCalling()
		                 ->andWhere(['or',
		                             [Request::field('consultant_id') => $chat_member_models_ids],
		                             [Objects::field('agent_id') => $old_users_ids]]);
	}

	/**
	 * @param string[] $model_types
	 *
	 * @return ChatMemberMessageQuery
	 * @throws ErrorException
	 */
	private function makeMessagesQuery(array $model_types): ChatMemberMessageQuery
	{
		return ChatMemberMessage::find()
		                        ->select([
			                        'id'                => ChatMemberMessage::field('id'),
			                        'to_chat_member_id' => ChatMemberMessage::field('to_chat_member_id')
		                        ])
		                        ->leftJoin(['chat_member_rel' => ChatMember::getTable()], [
			                        'chat_member_rel.id' => ChatMemberMessage::xfield('to_chat_member_id')
		                        ])
		                        ->andWhere(['chat_member_rel.model_type' => $model_types])
		                        ->notDeleted();
	}
}
