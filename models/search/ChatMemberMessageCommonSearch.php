<?php

namespace app\models\search;

use app\helpers\SQLHelper;
use app\kernel\common\models\AQ\AQ;
use app\kernel\common\models\exceptions\ValidateException;
use app\kernel\common\models\Form\Form;
use app\models\ActiveQuery\SurveyQuery;
use app\models\ChatMember;
use app\models\ChatMemberMessage;
use app\models\Contact;
use yii\base\ErrorException;
use yii\data\ActiveDataProvider;

class ChatMemberMessageCommonSearch extends Form
{
	public $id;
	public $to_chat_member_id;
	public $from_chat_member_id;
	public $message;
	public $survey_id;

	public $search;
	public $from_user_id;

	public $is_pinned = false;

	public function rules(): array
	{
		return [
			[['id', 'to_chat_member_id', 'survey_id', 'from_chat_member_id', 'from_user_id'], 'integer'],
			['message', 'safe'],
			['search', 'string'],
			['is_pinned', 'boolean'],
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
			                          ChatMemberMessage::field('*')
		                          ])
		                          ->with(['fromChatMember.user.userProfile'])
		                          ->with(['toChatMember.objectChatMember', 'toChatMember.request', 'toChatMember.user.userProfile', 'toChatMember.company'])
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
		                          ->groupBy([ChatMemberMessage::field('id')])
		                          ->orderBy([ChatMemberMessage::field('id') => SORT_DESC]);

		$dataProvider = new ActiveDataProvider([
			'query'      => $query,
			'pagination' => [
				'defaultPageSize' => 30,
				'pageSizeLimit'   => [0, 50],
			],
			'sort'       => false,
		]);

		$query->andFilterWhere([
			ChatMemberMessage::field('id')                  => $this->id,
			ChatMemberMessage::field('to_chat_member_id')   => $this->to_chat_member_id,
			ChatMemberMessage::field('from_chat_member_id') => $this->from_chat_member_id
		]);

		$query->andFilterWhere(['like', ChatMemberMessage::field('message'), $this->message]);

		if (!empty($this->survey_id)) {
			$query->innerJoinWith(['surveys surveys' => function (SurveyQuery $subQuery) {
				$subQuery->andWhere(['surveys.id' => $this->survey_id])->andWhere(['surveys.deleted_at' => null]);
			}]);
		}

		if ($this->isFilterTrue($this->is_pinned)) {
			$query->innerJoin(['chatMember' => $this->makeChatMemberQuery()], ChatMemberMessage::field('to_chat_member_id') . ' = ' . 'chatMember.id');
		}

		if ($this->hasFilter($this->from_user_id)) {
			$query->joinWith(['fromChatMember fcm']);

			$query->andFilterWhere([
				'fcm.model_id' => $this->from_user_id
			]);
		}

		if ($this->hasFilter($this->search)) {
			$query->joinWith(['contacts']);

			$query->andFilterWhere([
				'or',
				['like', ChatMemberMessage::field('id'), $this->search],
				['like', ChatMemberMessage::field('message'), $this->search],
				[
					'like',
					SQLHelper::concatWithCoalesce([
						Contact::field('first_name'),
						Contact::field('middle_name'),
						Contact::field('last_name')
					]),
					$this->search
				],
			]);
		}

		return $dataProvider;
	}

	/**
	 * @throws ErrorException
	 */
	private function makeChatMemberQuery(): AQ
	{
		return ChatMember::find()
		                 ->select(['id', 'pinned_chat_member_message_id'])
		                 ->andWhere(['is not', ChatMember::field('pinned_chat_member_message_id'), null]);
	}
}
