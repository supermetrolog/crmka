<?php

namespace app\models\search;

use app\components\ExpressionBuilder\IfExpressionBuilder;
use app\kernel\common\models\AQ\AQ;
use app\kernel\common\models\exceptions\ValidateException;
use app\kernel\common\models\Form\Form;
use app\models\ChatMember;
use app\models\ChatMemberLastEvent;
use app\models\ChatMemberMessage;
use app\models\ChatMemberMessageView;
use app\models\Company;
use app\models\Notification\UserNotification;
use app\models\ObjectChatMember;
use app\models\Objects;
use app\models\Relation;
use app\models\Reminder;
use app\models\Request;
use app\models\Task;
use app\models\TaskObserver;
use app\models\User;
use app\models\UserProfile;
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
	public $search;
	public $status;

	public $current_chat_member_id;
	public $current_user_id;

	public function rules(): array
	{
		return [
			[['id', 'model_id', 'company_id', 'object_id', 'status'], 'integer'],
			[['model_type', 'created_at', 'updated_at', 'search'], 'safe'],
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
		$messageQuery = ChatMemberMessage::find()
		                                 ->select(['to_chat_member_id', 'chat_member_message_id' => 'MAX(id)'])
		                                 ->notDeleted()
		                                 ->groupBy(['to_chat_member_id']);

		$eventQuery = ChatMemberLastEvent::find()
		                                 ->where(['chat_member_id' => $this->current_chat_member_id]);

		$query = ChatMemberSearchView::find()
		                             ->select([
			                             ChatMember::getColumn('*'),
			                             'last_call_rel_id'          => 'last_call_rel.id',
			                             'is_linked'                 => '(cmle.id is not null)',
			                             'unread_task_count'         => 'COUNT(DISTINCT t.id)',
			                             'unread_reminder_count'     => 'COUNT(DISTINCT r.id)',
			                             'unread_notification_count' => 'COUNT(DISTINCT un.id)',
			                             'unread_message_count'      => 'COUNT(DISTINCT m.id)',
		                             ])
		                             ->leftJoinLastCallRelation()
		                             ->leftJoin(['t' => $this->makeTaskQuery()], [
			                             't.to_chat_member_id' => new Expression(ChatMember::field('id'))
		                             ])
		                             ->leftJoin(['r' => $this->makeReminderQuery()], [
			                             'r.to_chat_member_id' => new Expression(ChatMember::field('id'))
		                             ])
		                             ->leftJoin(['un' => $this->makeNotificationQuery()], [
			                             'un.to_chat_member_id' => new Expression(ChatMember::field('id'))
		                             ])
		                             ->leftJoin(['m' => $this->makeMessageQuery()], [
			                             'm.to_chat_member_id' => new Expression(ChatMember::field('id'))
		                             ])
		                             ->leftJoin(['cmm' => $messageQuery], ChatMember::getColumn('id') . '=' . 'cmm.to_chat_member_id')
		                             ->leftJoin(['cmle' => $eventQuery], ChatMember::getColumn('id') . '=' . 'cmle.event_chat_member_id')
		                             ->joinWith([
			                             'objectChatMember.object',
			                             'request',
			                             'user'
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
			'sort'  => [
				'enableMultiSort' => true,
				'defaultOrder'    => [
					'default' => SORT_DESC
				],
				'attributes'      => [
					'task'         => [
						'asc'  => ['t.id' => SORT_ASC],
						'desc' => ['t.id' => SORT_DESC]
					],
					'notification' => [
						'asc'  => ['un.id' => SORT_ASC],
						'desc' => ['un.id' => SORT_DESC]
					],
					'message'      => [
						'asc'  => ['cmm.chat_member_message_id' => SORT_ASC],
						'desc' => ['cmm.chat_member_message_id' => SORT_DESC]
					],
					'call'         => [
						'asc'  => [
							'last_call_rel.created_at'                                        => SORT_ASC,
							'IF (request.updated_at, request.updated_at, request.created_at)' => SORT_ASC,
							IfExpressionBuilder::create()
							                   ->condition(Objects::field('last_update'))
							                   ->left(Objects::field('last_update'))
							                   ->right(Objects::field('publ_time'))
							                   ->beforeBuild(fn($expression) => "$expression ASC")
							                   ->build()
						],
						'desc' => [
							'last_call_rel.created_at'                                        => SORT_DESC,
							'IF (request.updated_at, request.updated_at, request.created_at)' => SORT_DESC,
							IfExpressionBuilder::create()
							                   ->condition(Objects::field('last_update'))
							                   ->left(Objects::field('last_update'))
							                   ->right(Objects::field('publ_time'))
							                   ->beforeBuild(fn($expression) => "$expression DESC")
							                   ->build()
						]
					],
					'default'      => [
						'asc'  => [
							'cmle.updated_at'            => SORT_ASC,
							'cmm.chat_member_message_id' => SORT_ASC,
							ChatMember::field('id')      => SORT_ASC,
						],
						'desc' => [
							'cmle.updated_at'            => SORT_DESC,
							'cmm.chat_member_message_id' => SORT_DESC,
							ChatMember::field('id')      => SORT_ASC,
						],
					]
				]
			]
		]);

		$this->load($params);

		$this->validateOrThrow();

		if (!empty($this->search)) {
			$query->leftJoin(['request_company' => Company::tableName()], ['request_company.id' => new Expression(Request::field('company_id'))]);
			$query->leftJoin(['object_company' => Company::tableName()], ['object_company.id' => new Expression(Objects::field('company_id'))]);
			$query->leftJoin(['user_profile' => UserProfile::tableName()], ['user_profile.user_id' => new Expression(User::field('id'))]);

			$query->andFilterWhere([
				'or',
				['like', 'request_company.nameEng', $this->search],
				['like', 'request_company.nameRu', $this->search],
				['like', 'object_company.nameEng', $this->search],
				['like', 'object_company.nameRu', $this->search],
				['like', Objects::field('address'), $this->search],
				[
					'like',
					sprintf(
						'concat(coalesce(%s, ""), " ", coalesce(%s, ""), " ", coalesce(%s, ""))',
						'user_profile.first_name',
						'user_profile.middle_name',
						'user_profile.last_name'),
					$this->search
				],
			]);

			$query->andWhere([
				'OR',
				['IS NOT', 'request_company.id', null],
				['IS NOT', 'object_company.id', null],
				['IS NOT', 'user_profile.id', null],
			]);
		}

		$query->orFilterWhere([Request::field('company_id') => $this->company_id])
		      ->orFilterWhere([Objects::field('company_id') => $this->company_id]);

		$query->andFilterWhere([
			ChatMember::field('id')              => $this->id,
			ChatMember::field('model_id')        => $this->model_id,
			ChatMember::field('model_type')      => $this->model_type,
			ChatMember::field('created_at')      => $this->created_at,
			ChatMember::field('updated_at')      => $this->updated_at,
			ObjectChatMember::field('object_id') => $this->object_id,
			User::field('status')                => $this->status
		]);

		return $dataProvider;
	}

	/**
	 * @throws ErrorException
	 */
	private function makeTaskQuery(): AQ
	{
		return Task::find()
		           ->select([
			           'id'                => Task::field('id'),
			           'to_chat_member_id' => ChatMemberMessage::field('to_chat_member_id'),
		           ])
		           ->leftJoin(Relation::getTable(), [
			           Relation::field('first_type')  => ChatMemberMessage::getMorphClass(),
			           Relation::field('second_type') => Task::getMorphClass(),
			           Relation::field('second_id')   => new Expression(Task::field('id')),
		           ])
		           ->leftJoin(ChatMemberMessage::getTable(), [
			           ChatMemberMessage::field('id') => new Expression(Relation::field('first_id')),
		           ])
		           ->leftJoin(['observer' => TaskObserver::getTable()], [
			           'observer.task_id'   => new Expression(Task::field('id')),
			           'observer.user_id'   => $this->current_user_id,
			           'observer.viewed_at' => null
		           ])
		           ->andWhere([
			           Task::field('user_id') => $this->current_user_id,
			           'observer.user_id'     => $this->current_user_id
		           ])
		           ->notCompleted()
		           ->notImpossible()
		           ->notDeleted();
	}

	/**
	 * @throws ErrorException
	 */
	private function makeReminderQuery(): AQ
	{
		return Reminder::find()
		               ->select([
			               'id'                => Reminder::field('id'),
			               'to_chat_member_id' => ChatMemberMessage::field('to_chat_member_id'),
		               ])
		               ->leftJoin(Relation::getTable(), [
			               Relation::field('first_type')  => ChatMemberMessage::getMorphClass(),
			               Relation::field('second_type') => Reminder::getMorphClass(),
			               Relation::field('second_id')   => new Expression(Reminder::field('id')),
		               ])
		               ->leftJoin(ChatMemberMessage::getTable(), [
			               ChatMemberMessage::field('id') => new Expression(Relation::field('first_id')),
		               ])
		               ->andWhere([Reminder::field('user_id') => $this->current_user_id])
		               ->notNotified()
		               ->notDeleted();
	}

	/**
	 * @throws ErrorException
	 */
	private function makeNotificationQuery(): AQ
	{
		return UserNotification::find()
		                       ->select([
			                       'id'                => UserNotification::field('id'),
			                       'to_chat_member_id' => ChatMemberMessage::field('to_chat_member_id'),
		                       ])
		                       ->leftJoin(Relation::getTable(), [
			                       Relation::field('first_type')  => ChatMemberMessage::getMorphClass(),
			                       Relation::field('second_type') => UserNotification::getMorphClass(),
			                       Relation::field('second_id')   => new Expression(UserNotification::field('id')),
		                       ])
		                       ->leftJoin(ChatMemberMessage::getTable(), [
			                       ChatMemberMessage::field('id') => new Expression(Relation::field('first_id')),
		                       ])
		                       ->andWhere([UserNotification::field('user_id') => $this->current_user_id])
		                       ->andWhereNull(UserNotification::field('viewed_at'));
	}

	/**
	 * @throws ErrorException
	 */
	private function makeMessageQuery(): AQ
	{
		$subQuery = ChatMemberMessageView::find()
		                                 ->andWhere([ChatMemberMessageView::field('chat_member_id') => $this->current_chat_member_id]);

		return ChatMemberMessage::find()
		                        ->select([
			                        'id'                => ChatMemberMessage::field('id'),
			                        'to_chat_member_id' => ChatMemberMessage::field('to_chat_member_id'),
		                        ])
		                        ->leftJoin(['views' => $subQuery], [
			                        'views.chat_member_message_id' => new Expression(ChatMemberMessage::field('id')),
		                        ])
		                        ->andWhere(['views.chat_member_id' => null])
		                        ->notDeleted();
	}
}
