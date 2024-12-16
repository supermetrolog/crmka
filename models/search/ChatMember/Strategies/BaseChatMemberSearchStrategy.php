<?php

namespace app\models\search\ChatMember\Strategies;

use app\components\ExpressionBuilder\IfExpressionBuilder;
use app\helpers\ArrayHelper;
use app\kernel\common\models\exceptions\ValidateException;
use app\models\ActiveQuery\ChatMemberMessageQuery;
use app\models\ActiveQuery\ChatMemberQuery;
use app\models\ChatMember;
use app\models\ChatMemberLastEvent;
use app\models\ChatMemberMessage;
use app\models\ChatMemberMessageView;
use app\models\views\ChatMemberSearchView;
use yii\base\ErrorException;
use yii\data\ActiveDataProvider;

abstract class BaseChatMemberSearchStrategy extends AbstractChatMemberSearchStrategy
{
	public $consultant_ids;

	public function rules(): array
	{
		return ArrayHelper::merge(
			parent::rules(),
			[
				[['consultant_ids'], 'each', 'rule' => ['integer']]
			]
		);
	}

	/**
	 * @throws ErrorException
	 * @throws ValidateException
	 */
	public function search(array $params): ActiveDataProvider
	{
		$this->load($params);
		$this->validateOrThrow();

		$query = $this->createBaseQuery();
		$this->applySpecificQuery($query, $params);

		$dataProvider = $this->createDataProvider($query);

		$this->applyBaseFilters($query);
		$this->applySpecificFilters($query, $params);

		return $dataProvider;
	}

	/**
	 * @throws ErrorException
	 */
	private function createBaseQuery(): ChatMemberQuery
	{
		$messageQuery = ChatMemberMessage::find()
		                                 ->select(['to_chat_member_id', 'chat_member_message_id' => 'MAX(id)'])
		                                 ->notDeleted()
		                                 ->groupBy(['to_chat_member_id']);

		$eventQuery = ChatMemberLastEvent::find()
		                                 ->where(['chat_member_id' => $this->current_chat_member_id]);

		return ChatMemberSearchView::find()
		                           ->select([
			                           ChatMember::field('*'),
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
		                           ->with(['lastCall.user.userProfile'])
		                           ->groupBy(ChatMember::field('id'));
	}

	private function createDataProvider(ChatMemberQuery $query): ActiveDataProvider
	{
		return new ActiveDataProvider([
			'query' => $query,
			'sort'  => [
				'enableMultiSort' => true,
				'defaultOrder'    => [
					'default' => SORT_DESC
				],
				'attributes'      => $this->getSortAttributes()
			]
		]);
	}

	private function getSortAttributes(): array
	{
		return ArrayHelper::merge(
			[
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
				'default'      => $this->getDefaultSort()
			],
			$this->getSpecificSort());
	}

	/**
	 * @throws ErrorException
	 */
	private function applyBaseFilters(ChatMemberQuery $query): void
	{
		$query->andFilterWhere([
			ChatMember::field('id')         => $this->id,
			ChatMember::field('model_id')   => $this->model_id,
			ChatMember::field('model_type') => $this->model_type,
			ChatMember::field('created_at') => $this->created_at,
			ChatMember::field('updated_at') => $this->updated_at
		]);
	}

	/**
	 * @return ChatMemberMessageQuery
	 * @throws ErrorException
	 */
	protected function makeMessageQuery(): ChatMemberMessageQuery
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

	abstract protected function applySpecificQuery(ChatMemberQuery $query, array $params): void;

	abstract protected function applySpecificFilters(ChatMemberQuery $query, array $params): void;

	abstract protected function getDefaultSort(): array;

	abstract protected function getSpecificSort(): array;
}
