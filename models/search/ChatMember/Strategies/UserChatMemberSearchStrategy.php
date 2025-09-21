<?php

namespace app\models\search\ChatMember\Strategies;

use app\helpers\ArrayHelper;
use app\helpers\SQLHelper;
use app\models\ActiveQuery\ChatMemberMessageQuery;
use app\models\ActiveQuery\ChatMemberQuery;
use app\models\ChatMember;
use app\models\ChatMemberMessage;
use app\models\ChatMemberMessageView;
use app\models\User\User;
use app\models\User\UserProfile;
use app\models\views\ChatMemberSearchView;
use yii\base\ErrorException;
use yii\db\Expression;

class UserChatMemberSearchStrategy extends AbstractChatMemberSearchStrategy
{
	public $status;

	public function rules(): array
	{
		return ArrayHelper::merge(
			parent::rules(),
			[
				['status', 'integer']
			]
		);
	}

	protected function createBaseQuery(): ChatMemberQuery
	{
		$messageQuery = ChatMemberMessage::find()
		                                 ->select(['to_chat_member_id', 'chat_member_message_id' => 'MAX(id)'])
		                                 ->notDeleted()
		                                 ->groupBy(['to_chat_member_id']);

		return ChatMemberSearchView::find()
		                           ->select([
			                           ChatMember::field('*'),
			                           'last_call_rel_id'          => 'last_call_rel.id',
			                           'is_linked'                 => $this->makeIsLinkedExpression(),
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
		                           ->joinWith(['user'])
		                           ->with('user.userProfile')
		                           ->with(['lastCall.user.userProfile'])
		                           ->groupBy(ChatMember::field('id'));
	}

	/**
	 * @throws ErrorException
	 */
	protected function getDefaultSort(): array
	{
		return [
			'asc'  => [
				'is_linked'                  => SORT_DESC,
				'cmm.chat_member_message_id' => SORT_ASC,
				ChatMember::field('id')      => SORT_ASC,
			],
			'desc' => [
				'is_linked'                  => SORT_DESC,
				'cmm.chat_member_message_id' => SORT_DESC,
				ChatMember::field('id')      => SORT_ASC,
			]
		];
	}

	/**
	 * @throws ErrorException
	 */
	protected function applySpecificFilters(ChatMemberQuery $query, array $params): void
	{
		if (!empty($this->search)) {
			$query->leftJoin(['user_profile' => UserProfile::tableName()], ['user_profile.user_id' => User::xfield('id')]);

			$query->andFilterWhere([
				'like',
				SQLHelper::concatWithCoalesce([
					'user_profile.first_name',
					'user_profile.middle_name',
					'user_profile.last_name'
				]),
				$this->search
			]);
		}

		$query->andFilterWhere([
			User::field('status') => $this->status
		]);
	}

	private function makeIsLinkedExpression(): Expression
	{
		return new Expression('COUNT(DISTINCT t.id) > 0 OR COUNT(DISTINCT un.id) > 0 OR COUNT(DISTINCT m.id) > 0');
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
		                        ->leftJoin(
			                        ['replied_messages' => ChatMemberMessage::tableName()],
			                        [
				                        'and',
				                        ['replied_messages.id' => ChatMemberMessage::xfield('reply_to_id')],
				                        ['replied_messages.from_chat_member_id' => $this->current_chat_member_id],
			                        ]
		                        )
		                        ->andWhere([
			                        'or',
			                        [ChatMemberMessage::field('to_chat_member_id') => $this->current_chat_member_id],
			                        ['is not', 'replied_messages.id', null],
		                        ])
		                        ->andWhere(['views.chat_member_id' => null])
		                        ->notDeleted();
	}

	protected function applySpecificQuery(ChatMemberQuery $query, array $params): void
	{
	}

	protected function getSpecificSort(): array
	{
		return [];
	}
}