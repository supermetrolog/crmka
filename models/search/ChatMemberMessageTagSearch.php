<?php

namespace app\models\search;

use app\kernel\common\models\exceptions\ValidateException;
use app\kernel\common\models\Form\Form;
use yii\data\ActiveDataProvider;
use app\models\ChatMemberMessageTag;

class ChatMemberMessageTagSearch extends Form
{
	public $id;
	public $name;
	public $created_at;
	public $updated_at;
	public $deleted_at;

	public function rules(): array
	{
		return [
			[['id'], 'integer'],
			[['name', 'created_at', 'updated_at', 'deleted_at'], 'safe'],
		];
	}

	/**
	 * @param array $params
	 *
	 * @return ActiveDataProvider
	 * @throws ValidateException
	 */
	public function search(array $params): ActiveDataProvider
	{
		$query = ChatMemberMessageTag::find();

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
		]);

		$this->load($params);

		$this->validateOrThrow();

		$query->andFilterWhere([
			'id'         => $this->id,
			'created_at' => $this->created_at,
			'updated_at' => $this->updated_at,
			'deleted_at' => $this->deleted_at,
		]);

		$query->andFilterWhere(['like', 'name', $this->name]);

		return $dataProvider;
	}
}
