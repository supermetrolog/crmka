<?php

declare(strict_types=1);

namespace app\repositories;

use app\dto\ChatMemberView\StatisticChatMemberViewDto;
use app\helpers\ArrayHelper;
use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\models\ActiveQuery\ChatMemberMessageQuery;
use app\models\ActiveQuery\ChatMemberQuery;
use app\models\ActiveQuery\TaskQuery;
use app\models\ActiveQuery\UserNotificationQuery;
use app\models\ChatMember;
use app\models\ChatMemberLastEvent;
use app\models\ChatMemberMessage;
use app\models\ChatMemberMessageView;
use app\models\Company\Company;
use app\models\Notification\UserNotification;
use app\models\Objects;
use app\models\Relation;
use app\models\Task;
use app\models\TaskObserver;
use app\models\User\User;
use app\models\views\ChatMemberStatisticView;
use yii\base\ErrorException;
use yii\db\Expression;

class ChatMemberRepository
{
	/**
	 * @return ChatMemberStatisticView[]
	 * @throws ErrorException
	 */
	public function getStatisticByIdsAndModelTypes(StatisticChatMemberViewDto $dto): array
	{
		$needCallingQuery = $this->makeNeedingCallCountQuery($dto->model_types);

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
			                                          'consultant_id'             => 'user.id',
			                                          'unread_task_count'         => 'tasks.unread_task_count',
			                                          'unread_notification_count' => 'notifications.unread_notification_count',
			                                          'unread_message_count'      => 'COUNT(DISTINCT messages.id) - COUNT(DISTINCT message_views.id)',
			                                          'outdated_call_count'       => 'COUNT(DISTINCT cls.id)'
		                                          ])
		                                          ->byModelType(User::getMorphClass())
		                                          ->joinWith(['user'])
		                                          ->leftJoin(['tasks' => $this->makeTaskQuery($dto->model_types)], [
			                                          'tasks.observer_id' => ChatMemberStatisticView::xfield('model_id')
		                                          ])
		                                          ->leftJoin(['notifications' => $this->makeNotificationQuery($dto->model_types)], [
			                                          'notifications.user_id' => ChatMemberStatisticView::xfield('model_id')
		                                          ])
		                                          ->leftJoin(['messages' => $this->makeMessagesQuery($dto->model_types, $dto->chat_member_ids)], [
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
		                                          ->leftJoin(['cls' => $needCallingQuery], ['or', 'cls.consultant_id = user.id', 'cls.consultant_id_old = user.user_id_old'])
		                                          ->andWhere([ChatMemberStatisticView::field('id') => $dto->chat_member_ids])
		                                          ->groupBy(ChatMemberStatisticView::field('id'));

		/** @var ChatMemberStatisticView[] $statistics */
		$statistics          = $chatMemberQuery->all();
		$allNeedingCallCount = (int)$needCallingQuery->count();

		foreach ($statistics as $statistic) {
			$statistic->outdated_call_count_all = $allNeedingCallCount;
		}

		return $statistics;
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
			           'observer_id'       => 'to.user_id',
			           'unread_task_count' => new Expression('COUNT(*)')
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
			           'chat_member.model_type' => $model_types
		           ])
		           ->notDeleted()
		           ->groupBy('to.user_id');
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
			                       'user_id'                   => UserNotification::field('user_id'),
			                       'unread_notification_count' => new Expression('COUNT(*)'),
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
		                       ])
		                       ->groupBy(UserNotification::field('user_id'));
	}

	/**
	 * @param string[] $model_types
	 *
	 * @return ChatMemberQuery
	 * @throws ErrorException
	 */
	private function makeNeedingCallCountQuery(array $model_types): ChatMemberQuery
	{
		return ChatMember::find()
		                 ->select(
			                 [
				                 'id'                => ChatMember::field('id'),
				                 'model_type'        => ChatMember::field('model_type'),
				                 'consultant_id'     => 'COALESCE(company.consultant_id, null)',
				                 'consultant_id_old' => Objects::field('agent_id')
			                 ]
		                 )
		                 ->leftJoinLastCallRelation()
		                 ->byModelTypes($model_types)
		                 ->joinWith(['objectChatMember.object', 'company'])
		                 ->needCalling()
		                 ->groupBy(['consultant_id', 'consultant_id_old']);
	}

	/**
	 * @param string[] $model_types
	 * @param int[]    $chat_member_ids
	 *
	 * @return ChatMemberMessageQuery
	 * @throws ErrorException
	 */
	private function makeMessagesQuery(array $model_types, array $chat_member_ids): ChatMemberMessageQuery
	{
		$query = ChatMemberMessage::find()
		                          ->select([
			                          'id'                => ChatMemberMessage::field('id'),
			                          'to_chat_member_id' => ChatMemberMessage::field('to_chat_member_id')
		                          ])
		                          ->leftJoin(['chat_member_rel' => ChatMember::getTable()], [
			                          'chat_member_rel.id' => ChatMemberMessage::xfield('to_chat_member_id')
		                          ])
		                          ->andWhere(['chat_member_rel.model_type' => $model_types])
		                          ->notDeleted();

		if (ArrayHelper::includes($model_types, User::getMorphClass())) {
			$query->leftJoin(
				['replied_messages' => ChatMemberMessage::tableName()],
				[
					'and',
					['replied_messages.id' => ChatMemberMessage::xfield('reply_to_id')],
					['replied_messages.from_chat_member_id' => $chat_member_ids],
				]
			);

			$query->andWhere([
				'or',
				[ChatMemberMessage::field('to_chat_member_id') => $chat_member_ids],
				['is not', 'replied_messages.id', null]
			]);
		}

		return $query;
	}


	public function getSystemChatMember(): ?ChatMember
	{
		$systemUser = User::find()->system()->one();

		if (!$systemUser) {
			return null;
		}

		return ChatMember::find()->byMorph($systemUser->id, User::getMorphClass())->one();
	}

	/**
	 * @throws ModelNotFoundException
	 */
	public function getByCompanyIdOrThrow(int $company_id): ChatMember
	{
		/** @var ChatMember */
		return ChatMember::find()->byMorph($company_id, Company::getMorphClass())->oneOrThrow();
	}
}
