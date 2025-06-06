<?php

namespace app\models\search;

use app\kernel\common\models\AQ\AQ;
use app\kernel\common\models\exceptions\ValidateException;
use app\kernel\common\models\Form\Form;
use app\models\ActiveQuery\SurveyQuery;
use app\models\ChatMemberMessage;
use app\models\ChatMemberMessageView;
use yii\base\ErrorException;
use yii\data\ActiveDataProvider;
use yii\db\Expression;

class ChatMemberMessageSearch extends Form
{
	public $id;
	public $to_chat_member_id;
	public $from_chat_member_id;
	public $message;
	public $created_at;
	public $updated_at;

	public $id_less_then;

	public $current_chat_member_id;
	public $survey_id;

	private const UNREAD_LIMIT     = 30;
	private const MAX_UNREAD_COUNT = 25;
	private const EXTRA_READ_COUNT = 5;

	public function rules(): array
	{
		return [
			[['id', 'to_chat_member_id', 'id_less_then', 'survey_id', 'from_chat_member_id'], 'integer'],
			[['message', 'created_at', 'updated_at'], 'safe'],
		];
	}

	/**
	 * @throws ValidateException
	 * @throws ErrorException
	 */
	public function search(array $params): ActiveDataProvider
	{
		$this->load($params);

		$this->validateOrThrow();

		$query = ChatMemberMessage::find()
		                          ->select([
			                          ChatMemberMessage::field('*'),
			                          '(views.view_id is not null) as is_viewed'
		                          ])
		                          ->leftJoin(['views' => $this->makeMessageViews()], [
			                          'views.id' => new Expression(ChatMemberMessage::field('id')),
		                          ])
		                          ->with(['fromChatMember.objectChatMember', 'fromChatMember.request'])
		                          ->with(['fromChatMember.user.userProfile'])
		                          ->with(['tasks.createdByUser.userProfile'])
		                          ->with(['alerts.createdByUser.userProfile'])
		                          ->with(['reminders.createdByUser.userProfile'])
		                          ->with(['contacts', 'tags', 'notifications', 'files'])
		                          ->with(['replyTo.fromChatMember.user.userProfile'])
		                          ->with([
			                          'surveys.user.userProfile',
			                          'surveys.contact.consultant.userProfile',
			                          'surveys.chatMember'
		                          ])
		                          ->notDeleted()
		                          ->orderBy([ChatMemberMessage::field('id') => SORT_DESC])
		                          ->limit(30);

		$unreadCount = $this->getUnreadMessageCount();

		if ($this->id_less_then === null && 0 < $unreadCount) {
			$query
				->orderBy([
					'(views.view_id is null)'      => SORT_DESC,
					ChatMemberMessage::field('id') => SORT_DESC,
				])
				->limit($unreadCount < self::MAX_UNREAD_COUNT ? self::UNREAD_LIMIT : $unreadCount + self::EXTRA_READ_COUNT);
		}

		$orderedQuery = ChatMemberMessage::find()
		                                 ->from(['messages' => $query])
		                                 ->orderBy(['messages.id' => SORT_ASC]);

		$dataProvider = new ActiveDataProvider([
			'query'      => $orderedQuery,
			'pagination' => false,
			'sort'       => false,
		]);

		$query->andFilterWhere([
			ChatMemberMessage::field('id')                  => $this->id,
			ChatMemberMessage::field('to_chat_member_id')   => $this->to_chat_member_id,
			ChatMemberMessage::field('from_chat_member_id') => $this->from_chat_member_id,
			ChatMemberMessage::field('created_at')          => $this->created_at,
			ChatMemberMessage::field('updated_at')          => $this->updated_at
		]);

		$query->andFilterWhere(['<', ChatMemberMessage::field('id'), $this->id_less_then]);

		$query->andFilterWhere(['like', ChatMemberMessage::field('message'), $this->message]);

		if (!empty($this->survey_id)) {
			$query->innerJoinWith(['surveys surveys' => function (SurveyQuery $subQuery) {
				$subQuery->andWhere(['surveys.id' => $this->survey_id])->andWhere(['surveys.deleted_at' => null]);
			}]);
		}

		return $dataProvider;
	}

	private function makeMessageViews(): AQ
	{
		return ChatMemberMessage::find()
		                        ->select([
			                        'id'                => ChatMemberMessage::field('id'),
			                        'to_chat_member_id' => ChatMemberMessage::field('to_chat_member_id'),
			                        'view_id'           => ChatMemberMessageView::field('id'),
		                        ])
		                        ->joinWith('views')
		                        ->andWhere([
			                        'or',
			                        [ChatMemberMessageView::field('chat_member_id') => $this->current_chat_member_id],
			                        [ChatMemberMessageView::field('chat_member_id') => null],
		                        ])
		                        ->notDeleted();
	}

	private function getUnreadMessageCount(): int
	{
		$subQuery = ChatMemberMessageView::find()
		                                 ->andWhere([ChatMemberMessageView::field('chat_member_id') => $this->current_chat_member_id]);

		return ChatMemberMessage::find()
		                        ->leftJoin(['views' => $subQuery], [
			                        'views.chat_member_message_id' => new Expression(ChatMemberMessage::field('id')),
		                        ])
		                        ->andFilterWhere([ChatMemberMessage::field('to_chat_member_id') => $this->to_chat_member_id])
		                        ->andWhere(['views.chat_member_id' => null])
		                        ->notDeleted()
		                        ->count();
	}
}
