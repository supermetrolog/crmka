<?php

namespace app\models\search;

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
	public $from_chat_member_id;
	public $message;
	public $created_at;
	public $updated_at;

	public $id_less_then;

	public $current_from_chat_member_id;

	public function rules(): array
	{
		return [
			[['id', 'to_chat_member_id', 'from_chat_member_id', 'id_less_then'], 'integer'],
			[['message', 'created_at', 'updated_at'], 'safe'],
		];
	}

	/**
	 * @throws ValidateException
	 */
	public function search(array $params): ActiveDataProvider
	{
		$query = ChatMemberMessage::find()
		                          ->notDeleted()
		                          ->orderBy(['id' => SORT_ASC])
		                          ->with(['fromChatMember.objectChatMember', 'fromChatMember.request'])
		                          ->with(['fromChatMember.user.userProfile'])
		                          ->with(['tasks.createdByUser.userProfile'])
		                          ->with(['alerts.createdByUser.userProfile'])
		                          ->with(['reminders.createdByUser.userProfile'])
		                          ->with(['contacts', 'tags', 'notifications', 'files']);

		$dataProvider = new ActiveDataProvider([
			'query'      => $query,
			'pagination' => false,
			'sort'       => false
		]);

		$this->load($params);

		$this->validateOrThrow();

		if ($this->id_less_then === null) {
			$query->joinWith('views')
			      ->andWhere([
					  ChatMemberMessage::getColumn('from_chat_member_id') => $this->current_from_chat_member_id,
					  ChatMemberMessageView::getColumn('id') => null,
			      ]);
		}

		$query->andFilterWhere([
			'id'                  => $this->id,
			'to_chat_member_id'   => $this->to_chat_member_id,
			'from_chat_member_id' => $this->from_chat_member_id,
			'created_at'          => $this->created_at,
			'updated_at'          => $this->updated_at,
		]);

		$query->andFilterWhere(['<', 'id', $this->id_less_then]);

		$query->andFilterWhere(['like', 'message', $this->message]);

		return $dataProvider;
	}
}
