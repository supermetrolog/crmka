<?php

namespace app\models\search;

use app\components\ExpressionBuilder\IfExpressionBuilder;
use app\helpers\StringHelper;
use app\kernel\common\models\exceptions\ValidateException;
use app\kernel\common\models\Form\Form;
use app\models\ActiveQuery\ChatMemberMessageQuery;
use app\models\ActiveQuery\TaskQuery;
use app\models\ActiveQuery\UserNotificationQuery;
use app\models\ChatMember;
use app\models\ChatMemberLastEvent;
use app\models\ChatMemberMessage;
use app\models\ChatMemberMessageView;
use app\models\Company;
use app\models\Notification\UserNotification;
use app\models\ObjectChatMember;
use app\models\Objects;
use app\models\Relation;
use app\models\Request;
use app\models\Task;
use app\models\TaskObserver;
use app\models\User;
use app\models\UserProfile;
use app\models\views\ChatMemberSearchView;
use yii\base\ErrorException;
use yii\data\ActiveDataProvider;

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
	public $consultant_ids;

	public $current_chat_member_id;
	public $current_user_id;

	public function rules(): array
	{
		return [
			[['id', 'model_id', 'company_id', 'object_id', 'status'], 'integer'],
			[['model_type', 'created_at', 'updated_at', 'search'], 'safe'],
			['consultant_ids', 'each', 'rule' => ['integer']],
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
	 * @param array $params
	 *
	 * @return ActiveDataProvider
	 * @throws ErrorException
	 * @throws ValidateException
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
			                             'unread_notification_count' => 'COUNT(DISTINCT un.id)',
			                             'unread_message_count'      => 'COUNT(DISTINCT m.id)',
		                             ])
		                             ->leftJoinLastCallRelation()
		                             ->leftJoin(['t' => $this->makeTaskQuery()], [
			                             't.to_chat_member_id' => ChatMember::xfield('id')
		                             ])
		                             ->leftJoin(['un' => $this->makeNotificationQuery()], [
			                             'un.to_chat_member_id' => ChatMember::xfield('id')
		                             ])
		                             ->leftJoin(['m' => $this->makeMessageQuery()], [
			                             'm.to_chat_member_id' => ChatMember::xfield('id')
		                             ])
		                             ->leftJoin(['cmm' => $messageQuery], ChatMember::getColumn('id') . '=' . 'cmm.to_chat_member_id')
		                             ->leftJoin(['cmle' => $eventQuery], ChatMember::getColumn('id') . '=' . 'cmle.event_chat_member_id')
		                             ->joinWith([
			                             'objectChatMember.object.consultant chm',
			                             'request',
			                             'user',
			                             'company'
		                             ])
		                             ->with(['lastCall.user.userProfile'])
		                             ->with(['objectChatMember.object.company', 'objectChatMember.object.offers'])
		                             ->with([
			                             'request.company',
			                             'request.regions',
			                             'request.directions',
			                             'request.districts',
			                             'request.objectTypes',
			                             'request.objectClasses',
		                             ])
		                             ->with(['user.userProfile'])
		                             ->with(['company.logo', 'company.categories', 'company.companyGroup', 'company.consultant'])
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
						'asc'  => [
							'is_linked' => SORT_DESC,
							IfExpressionBuilder::create()
							                   ->condition('COUNT(DISTINCT m.id) > 0')
							                   ->left('cmm.chat_member_message_id')
							                   ->right('NULL')
							                   ->beforeBuild(fn($expression) => "$expression ASC")
							                   ->build()
						],
						'desc' => [
							'is_linked' => SORT_DESC,
							IfExpressionBuilder::create()
							                   ->condition('COUNT(DISTINCT m.id) > 0')
							                   ->left('cmm.chat_member_message_id')
							                   ->right('NULL')
							                   ->beforeBuild(fn($expression) => "$expression DESC")
							                   ->build()
						]
					],
					'call'         => [
						'asc'  => [
							IfExpressionBuilder::create()
							                   ->condition(Request::field('consultant_id') . ' = ' . $this->current_user_id)
							                   ->left('1')
							                   ->right('0')
							                   ->beforeBuild(fn($expression) => "$expression DESC")
							                   ->build(),
							'last_call_rel.created_at'                                        => SORT_ASC,
							'IF (request.updated_at, request.updated_at, request.created_at)' => SORT_ASC,
							IfExpressionBuilder::create()
							                   ->condition(Objects::field('last_update'))
							                   ->left(Objects::field('last_update'))
							                   ->right(Objects::field('publ_time'))
							                   ->beforeBuild(fn($expression) => "$expression ASC")
							                   ->build(),
							Company::field('updated_at')                                      => SORT_ASC
						],
						'desc' => [
							IfExpressionBuilder::create()
							                   ->condition(Request::field('consultant_id') . ' = ' . $this->current_user_id)
							                   ->left('1')
							                   ->right('0')
							                   ->beforeBuild(fn($expression) => "$expression DESC")
							                   ->build(),
							'last_call_rel.created_at'                                        => SORT_DESC,
							'IF (request.updated_at, request.updated_at, request.created_at)' => SORT_DESC,
							IfExpressionBuilder::create()
							                   ->condition(Objects::field('last_update'))
							                   ->left(Objects::field('last_update'))
							                   ->right(Objects::field('publ_time'))
							                   ->beforeBuild(fn($expression) => "$expression DESC")
							                   ->build(),
							Company::field('updated_at')                                      => SORT_DESC
						]
					],
					'default'      => [
						'asc'  => [
							'cmle.updated_at'            => SORT_ASC,
							'cmm.chat_member_message_id' => SORT_ASC,
							IfExpressionBuilder::create()
							                   ->condition(Objects::field('last_update'))
							                   ->left(Objects::field('last_update'))
							                   ->right(Objects::field('publ_time'))
							                   ->beforeBuild(fn($expression) => "$expression ASC")
							                   ->build(),
							Company::field('updated_at') => SORT_ASC,
							ChatMember::field('id')      => SORT_ASC,
						],
						'desc' => [
							'cmle.updated_at'            => SORT_DESC,
							'cmm.chat_member_message_id' => SORT_DESC,
							IfExpressionBuilder::create()
							                   ->condition(Objects::field('last_update'))
							                   ->left(Objects::field('last_update'))
							                   ->right(Objects::field('publ_time'))
							                   ->beforeBuild(fn($expression) => "$expression DESC")
							                   ->build(),
							Company::field('updated_at') => SORT_DESC,
							ChatMember::field('id')      => SORT_ASC,
						],
					]
				]
			]
		]);

		$this->load($params);

		$this->validateOrThrow();

		if (!empty($this->search)) {
			$query->leftJoin(['request_company' => Company::tableName()], ['request_company.id' => Request::xfield('company_id')]);
			$query->leftJoin(['object_company' => Company::tableName()], ['object_company.id' => Objects::xfield('company_id')]);
			$query->leftJoin(['user_profile' => UserProfile::tableName()], ['user_profile.user_id' => User::xfield('id')]);

			$searchWords = StringHelper::explode(StringHelper::SYMBOL_SPACE, $this->search);

			$query->andFilterWhere([
				'or',
				['like', 'request_company.nameEng', $searchWords],
				['like', 'request_company.nameRu', $searchWords],
				['like', 'object_company.nameEng', $searchWords],
				['like', 'object_company.nameRu', $searchWords],
				['like', Objects::field('address'), $searchWords],
				[
					'like',
					sprintf(
						'concat(coalesce(%s, ""), " ", coalesce(%s, ""), " ", coalesce(%s, ""))',
						'user_profile.first_name',
						'user_profile.middle_name',
						'user_profile.last_name'),
					$searchWords
				],
				['like', Company::field('nameEng'), $searchWords],
				['like', Company::field('nameRu'), $searchWords],
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

		$query->andFilterWhere([
			'or',
			[Company::field('consultant_id') => $this->consultant_ids],
			['chm.id' => $this->consultant_ids]
		]);

		return $dataProvider;
	}

	/**
	 * @return TaskQuery
	 * @throws ErrorException
	 */
	private function makeTaskQuery(): TaskQuery
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
	private function makeNotificationQuery(): UserNotificationQuery
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

	/**
	 * @return ChatMemberMessageQuery
	 * @throws ErrorException
	 */
	private function makeMessageQuery(): ChatMemberMessageQuery
	{
		$subQuery = ChatMemberMessageView::find()
		                                 ->andWhere([ChatMemberMessageView::field('chat_member_id') => $this->current_chat_member_id]);

		return ChatMemberMessage::find()
		                        ->select([
			                        'id'                => ChatMemberMessage::field('id'),
			                        'to_chat_member_id' => ChatMemberMessage::field('to_chat_member_id'),
		                        ])
		                        ->leftJoin(['views' => $subQuery], [
			                        'views.chat_member_message_id' => ChatMemberMessage::xfield('id'),
		                        ])
		                        ->andWhere(['views.chat_member_id' => null])
		                        ->notDeleted();
	}
}
