<?php

namespace app\models\search;

use app\helpers\DumpHelper;
use app\kernel\common\models\AQ\AQ;
use app\kernel\common\models\exceptions\ValidateException;
use app\kernel\common\models\Form\Form;
use app\models\ChatMember;
use app\models\ChatMemberMessage;
use app\models\ChatMemberMessageView;
use app\models\Notification\UserNotification;
use app\models\ObjectChatMember;
use app\models\Objects;
use app\models\Relation;
use app\models\Reminder;
use app\models\Request;
use app\models\Task;
use app\models\views\ChatMemberSearchView;
use yii\base\ErrorException;
use yii\data\ActiveDataProvider;
use yii\db\Expression;

class ChatMemberSearch extends Form
{
	public $id;
	public $model_type;
	public $model_id;
	public $created_at;
	public $updated_at;

	public $company_id;
	public $object_id;

	public $current_user_id;
	public $current_from_chat_member_id;

	public function rules(): array
	{
		return [
			[['id', 'model_id', 'company_id', 'object_id'], 'integer'],
			[['model_type', 'created_at', 'updated_at'], 'safe'],
		];
	}

	/**
	 * TODO: add in generator template
	 *
	 * @return string
	 */
	public function formName(): string
	{
		return '';
	}

	/**
	 * @throws ValidateException
	 * @throws ErrorException
	 */
	public function search(array $params): ActiveDataProvider
	{
		$query = ChatMemberSearchView::find()
		                             ->select([
			                             ChatMember::getColumn('*'),
			                             'last_call_rel_id'          => 'last_call_rel.id',
			                             'unread_task_count'         => 'COUNT(DISTINCT t.id)',
			                             'unread_reminder_count'     => 'COUNT(DISTINCT r.id)',
			                             'unread_notification_count' => 'COUNT(DISTINCT un.id)',
			                             'unread_message_count'      => 'COUNT(DISTINCT m.id)',
		                             ])
		                             ->leftJoinLastCallRelation()
		                             ->leftJoin(['t' => $this->makeTaskQuery()], ['t.to_chat_member_id' => new Expression(ChatMember::field('id'))])
		                             ->leftJoin(['r' => $this->makeReminderQuery()], ['r.to_chat_member_id' => new Expression(ChatMember::field('id'))])
		                             ->leftJoin(['un' => $this->makeNotificationQuery()], ['un.to_chat_member_id' => new Expression(ChatMember::field('id'))])
		                             ->leftJoin(['m' => $this->makeMessageQuery()], ['m.to_chat_member_id' => new Expression(ChatMember::field('id'))])
		                             ->joinWith([
			                             'objectChatMember.object',
			                             'request'
		                             ])
		                             ->with(['lastCall.user.userProfile'])
		                             ->with(['objectChatMember.object.company'])
		                             ->with([
			                             'request.company',
			                             'request.regions',
			                             'request.directions',
			                             'request.districts',
			                             'request.objectTypes',
			                             'request.objectClasses',
		                             ])
		                             ->with(['user.userProfile'])
		                             ->groupBy(ChatMember::field('id'));

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
		]);

		$this->load($params);

		$this->validateOrThrow();


		$query->orFilterWhere([Request::field('company_id') => $this->company_id])
		      ->orFilterWhere([Objects::field('company_id') => $this->company_id]);

		$query->andFilterWhere([
			ChatMember::field('id')              => $this->id,
			ChatMember::field('model_id')        => $this->model_id,
			ChatMember::field('model_type')      => $this->model_type,
			ChatMember::field('created_at')      => $this->created_at,
			ChatMember::field('updated_at')      => $this->updated_at,
			ObjectChatMember::field('object_id') => $this->object_id
		]);


		return $dataProvider;
	}

	/**
	 * @throws ErrorException
	 */
	private function makeTaskQuery(): AQ
	{
		$subQuery = Task::find()
		                ->notCompleted()
		                ->notDeleted();

		return ChatMemberMessage::find()
		                        ->select([
			                        'to_chat_member_id' => ChatMemberMessage::field('to_chat_member_id'),
			                        'id'                => 'tasks.id',
		                        ])
		                        ->leftJoin(Relation::getTable(), [
			                        Relation::field('first_id')    => new Expression(ChatMemberMessage::field('id')),
			                        Relation::field('first_type')  => ChatMemberMessage::getMorphClass(),
			                        Relation::field('second_type') => Task::getMorphClass(),
		                        ])
		                        ->leftJoin(['tasks' => $subQuery], [
			                        'tasks.id'      => new Expression(Relation::field('second_id')),
			                        'tasks.user_id' => $this->current_user_id,
		                        ]);
	}

	/**
	 * @throws ErrorException
	 */
	private function makeReminderQuery(): AQ
	{
		$subQuery = Reminder::find()
		                    ->notNotified()
		                    ->notDeleted();

		return ChatMemberMessage::find()
		                        ->select([
			                        'to_chat_member_id' => ChatMemberMessage::field('to_chat_member_id'),
			                        'id'                => 'reminders.id',
		                        ])
		                        ->leftJoin(Relation::getTable(), [
			                        Relation::field('first_id')    => new Expression(ChatMemberMessage::field('id')),
			                        Relation::field('first_type')  => ChatMemberMessage::getMorphClass(),
			                        Relation::field('second_type') => Reminder::getMorphClass(),
		                        ])
		                        ->leftJoin(['reminders' => $subQuery], [
			                        'reminders.id'      => new Expression(Relation::field('second_id')),
			                        'reminders.user_id' => $this->current_user_id,
		                        ]);
	}

	/**
	 * @throws ErrorException
	 */
	private function makeNotificationQuery(): AQ
	{
		$subQuery = UserNotification::find()
		                            ->andWhereNull('viewed_at');

		return ChatMemberMessage::find()
		                        ->select([
			                        'to_chat_member_id' => ChatMemberMessage::field('to_chat_member_id'),
			                        'id'                => 'notification.id',
		                        ])
		                        ->leftJoin(Relation::getTable(), [
			                        Relation::field('first_id')    => new Expression(ChatMemberMessage::field('id')),
			                        Relation::field('first_type')  => ChatMemberMessage::getMorphClass(),
			                        Relation::field('second_type') => UserNotification::getMorphClass(),
		                        ])
		                        ->leftJoin(['notification' => $subQuery], [
			                        'notification.id'      => new Expression(Relation::field('second_id')),
			                        'notification.user_id' => $this->current_user_id,
		                        ]);
	}

	/**
	 * @throws ErrorException
	 */
	private function makeMessageQuery(): AQ
	{
		return ChatMemberMessage::find()
		                        ->select([
			                        'to_chat_member_id' => ChatMemberMessage::field('to_chat_member_id'),
			                        'id'                => ChatMemberMessage::field('id'),
		                        ])
		                        ->joinWith('views')
		                        ->byFromChatMemberId($this->current_from_chat_member_id)
		                        ->andWhereNull(ChatMemberMessageView::getColumn('id'))
		                        ->notDeleted();
	}
}
