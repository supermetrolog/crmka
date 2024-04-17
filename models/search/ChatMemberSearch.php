<?php

namespace app\models\search;

use app\exceptions\domain\model\ValidateException;
use app\kernel\common\models\Form;
use app\models\ActiveQuery\ChatMemberMessageQuery;
use yii\data\ActiveDataProvider;
use app\models\ChatMember;

class ChatMemberSearch extends Form
{
	public $id;
	public $model_type;
	public $model_id;
	public $created_at;
	public $updated_at;

	public function rules(): array
	{
		return [
			[['id', 'model_id'], 'integer'],
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
	 */
	public function search(array $params): ActiveDataProvider
	{
		$query = ChatMember::find()
		                   ->with(['messages' => function (ChatMemberMessageQuery $query) {
			                   $query->notDeleted();
		                   }])
		                   ->with(['toChatMemberMessages', 'objectChatMember.object', 'request']);

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
		]);

		$this->load($params);

		$this->validateOrThrow();

		$query->andFilterWhere([
			'id'         => $this->id,
			'model_id'   => $this->model_id,
			'model_type' => $this->model_type,
			'created_at' => $this->created_at,
			'updated_at' => $this->updated_at,
		]);

		return $dataProvider;
	}
}
