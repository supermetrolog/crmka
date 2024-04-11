<?php

namespace app\models\search;

use app\exceptions\domain\model\ValidateException;
use app\kernel\common\models\Form;
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
	 * @throws ValidateException
	 */
	public function search(array $params): ActiveDataProvider
	{
		$query = ChatMember::find()->with(['request']);

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
		]);

		$this->load($params);

		$this->validateOrThrow();

		$query->andFilterWhere([
			'id'         => $this->id,
			'model_id'   => $this->model_id,
			'created_at' => $this->created_at,
			'updated_at' => $this->updated_at,
		]);

		$query->andFilterWhere(['like', 'model_type', $this->model_type]);

		return $dataProvider;
	}
}
