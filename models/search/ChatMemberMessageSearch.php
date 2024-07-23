<?php

namespace app\models\search;

use app\kernel\common\models\AR\AR;
use app\kernel\common\models\exceptions\ValidateException;
use app\kernel\common\models\Form\Form;
use app\models\ChatMemberMessage;
use app\models\ChatMemberMessageView;
use yii\data\ActiveDataProvider;
use yii\data\Pagination;
use yii\db\Expression;

class ChatMemberMessageSearch extends Form
{
	public $id;
	public $to_chat_member_id;
	public $message;
	public $created_at;
	public $updated_at;

	public $id_less_then;

	public $current_from_chat_member_id;

	private const UNREAD_LIMIT     = 30;
	private const MAX_UNREAD_COUNT = 25;
	private const EXTRA_READ_COUNT = 5;

	public function rules(): array
	{
		return [
			[['id', 'to_chat_member_id', 'id_less_then'], 'integer'],
			[['message', 'created_at', 'updated_at'], 'safe'],
		];
	}

	/**
	 * @throws ValidateException
	 */
	public function search(array $params): ActiveDataProvider
	{
		$this->load($params);

		$this->validateOrThrow();

		$unreadCount = ChatMemberMessage::find()
		                                ->joinWith('views')
		                                ->andWhereNull(ChatMemberMessageView::getColumn('id'))
		                                ->andFilterWhere([
			                                ChatMemberMessage::field('to_chat_member_id') => $this->to_chat_member_id,
		                                ])
		                                ->byFromChatMemberId($this->current_from_chat_member_id)
		                                ->notDeleted()
		                                ->count();

		$query = ChatMemberMessage::find()
		                          ->select([
			                          ChatMemberMessage::field('*'),
			                          '(chat_member_message_view.chat_member_message_id IS NOT NULL) as is_viewed'
		                          ])
		                          ->joinWith('fromChatMemberViews')
		                          ->with(['fromChatMember.objectChatMember', 'fromChatMember.request'])
		                          ->with(['fromChatMember.user.userProfile'])
		                          ->with(['tasks.createdByUser.userProfile'])
		                          ->with(['alerts.createdByUser.userProfile'])
		                          ->with(['reminders.createdByUser.userProfile'])
		                          ->with(['contacts', 'tags', 'notifications', 'files'])
		                          ->byFromChatMemberId($this->current_from_chat_member_id)
		                          ->notDeleted()
		                          ->orderBy([ChatMemberMessage::field('id') => SORT_DESC])
		                          ->limit(30);

		if ($this->id_less_then === null && 0 < $unreadCount) {
			$query->joinWith('views')
			      ->orderBy([
				      '(CASE WHEN ' . ChatMemberMessageView::field('id') . ' IS NULL THEN 0 ELSE 1 END)' => SORT_ASC,
				      ChatMemberMessage::field('id')                                                     => SORT_DESC,
			      ])
			      ->limit($unreadCount < self::MAX_UNREAD_COUNT ? self::UNREAD_LIMIT : $unreadCount + self::EXTRA_READ_COUNT);
		}

		$orderedQuery = ChatMemberMessage::find()->from(['messages' => $query])->orderBy(['messages.id' => SORT_ASC]);

		$dataProvider = new ActiveDataProvider([
			'query'      => $orderedQuery,
			'pagination' => false,
			'sort'       => false,
		]);

		$query->andFilterWhere([
			ChatMemberMessage::field('id')                => $this->id,
			ChatMemberMessage::field('to_chat_member_id') => $this->to_chat_member_id,
			ChatMemberMessage::field('created_at')        => $this->created_at,
			ChatMemberMessage::field('updated_at')        => $this->updated_at,
		]);

		$query->andFilterWhere(['<', ChatMemberMessage::field('id'), $this->id_less_then]);

		$query->andFilterWhere(['like', ChatMemberMessage::field('message'), $this->message]);

		return $dataProvider;
	}
}
