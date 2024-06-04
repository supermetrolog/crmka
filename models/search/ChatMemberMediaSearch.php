<?php

namespace app\models\search;

use app\kernel\common\models\exceptions\ValidateException;
use app\kernel\common\models\Form\Form;
use app\models\ChatMemberMessage;
use app\models\Media;
use app\models\Relation;
use yii\base\ErrorException;
use yii\data\ActiveDataProvider;

class ChatMemberMediaSearch extends Form
{
	public $to_member_chat_id;
	public $from_member_chat_id;

	public $extension;

	public function rules(): array
	{
		return [
			[['to_member_chat_id', 'from_member_chat_id'], 'integer'],
			[['extension'], 'string'],
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
		$query = Media::find()
		    ->select(Media::getColumn('*'))
			->leftJoin(Relation::getTable(), Relation::getColumn('second_id') . '=' . Media::getColumn('id'))
			->leftJoin(ChatMemberMessage::getTable(), Relation::getColumn('first_id') . '=' . ChatMemberMessage::getColumn('id'))
			->where([Relation::getColumn('second_type') => 'media', Relation::getColumn('first_type') => 'chat_member_message'])
			->andWhereNull(ChatMemberMessage::getColumn('deleted_at'))
			->notDeleted();

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
		]);

		$this->load($params);

		$this->validateOrThrow();

		$query->andWhere([ChatMemberMessage::getColumn('to_chat_member_id') => $this->to_member_chat_id]);
		$query->andWhere([ChatMemberMessage::getColumn('from_chat_member_id') => $this->from_member_chat_id]);

		$query->andFilterWhere(['like', 'extension', $this->extension]);

		return $dataProvider;
	}
}