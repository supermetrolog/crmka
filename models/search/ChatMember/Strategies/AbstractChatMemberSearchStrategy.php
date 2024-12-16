<?php

namespace app\models\search\ChatMember\Strategies;

use app\kernel\common\models\Form\Form;
use app\models\ActiveQuery\ChatMemberMessageQuery;
use app\models\ActiveQuery\TaskQuery;
use app\models\ActiveQuery\UserNotificationQuery;
use app\models\ChatMemberMessage;
use app\models\Notification\UserNotification;
use app\models\Relation;
use app\models\search\ChatMember\ChatMemberSearchStrategyInterface;
use app\models\Task;
use app\models\TaskObserver;
use yii\base\ErrorException;

abstract class AbstractChatMemberSearchStrategy extends Form implements ChatMemberSearchStrategyInterface
{
	public $current_chat_member_id;
	public $current_user_id;

	public $id;
	public $model_type;
	public $model_id;
	public $created_at;
	public $updated_at;

	public $search;

	public function rules(): array
	{
		return [
			[['id', 'model_id'], 'integer'],
			[['model_type', 'created_at', 'updated_at', 'search'], 'safe']
		];
	}

	/**
	 * @throws ErrorException
	 */
	protected function makeTaskQuery(): TaskQuery
	{
		return Task::find()
		           ->select([
			           'id'                => Task::field('id'),
			           'to_chat_member_id' => ChatMemberMessage::field('to_chat_member_id'),
		           ])
		           ->leftJoin('relation FORCE INDEX (`idx-relation-second_id-second_type`)', [
			           Relation::field('first_type')  => ChatMemberMessage::getMorphClass(),
			           Relation::field('second_type') => Task::getMorphClass(),
			           Relation::field('second_id')   => Task::xfield('id'),
		           ])
		           ->leftJoin(ChatMemberMessage::getTable(), [
			           ChatMemberMessage::field('id') => Relation::xfield('first_id'),
		           ])
		           ->leftJoin(['observer' => TaskObserver::getTable()], [
			           'observer.task_id'   => Task::xfield('id'),
			           'observer.user_id'   => $this->current_user_id,
			           'observer.viewed_at' => null
		           ])
		           ->andWhere([
			           'observer.user_id' => $this->current_user_id
		           ])
		           ->notDeleted();
	}

	/**
	 * @return UserNotificationQuery
	 * @throws ErrorException
	 */
	protected function makeNotificationQuery(): UserNotificationQuery
	{
		return UserNotification::find()
		                       ->select([
			                       'id'                => UserNotification::field('id'),
			                       'to_chat_member_id' => ChatMemberMessage::field('to_chat_member_id'),
		                       ])
		                       ->leftJoin(Relation::getTable(), [
			                       Relation::field('first_type')  => ChatMemberMessage::getMorphClass(),
			                       Relation::field('second_type') => UserNotification::getMorphClass(),
			                       Relation::field('second_id')   => UserNotification::xfield('id'),
		                       ])
		                       ->leftJoin(ChatMemberMessage::getTable(), [
			                       ChatMemberMessage::field('id') => Relation::xfield('first_id'),
		                       ])
		                       ->andWhere([UserNotification::field('user_id') => $this->current_user_id])
		                       ->andWhereNull(UserNotification::field('viewed_at'));
	}

	abstract protected function makeMessageQuery(): ChatMemberMessageQuery;
}
