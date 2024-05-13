<?php

namespace app\models\search;

use app\kernel\common\models\exceptions\ValidateException;
use app\kernel\common\models\Form\Form;
use yii\data\ActiveDataProvider;
use app\models\Alert;

class AlertSearch extends Form
{
	public $id;
	public $user_id;
	public $message;
	public $created_by_type;
	public $created_by_id;
	public $created_at;
	public $updated_at;
	public $deleted_at;

	public function rules(): array
	{
		return [
			[['id', 'user_id', 'created_by_id'], 'integer'],
			[['message', 'created_by_type', 'created_at', 'updated_at', 'deleted_at'], 'safe'],
		];
	}

	/**
	 * @throws ValidateException
	 */
	public function search(array $params): ActiveDataProvider
	{
		$query = Alert::find();

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
		]);

		$this->load($params);

		$this->validateOrThrow();

		$query->andFilterWhere([
			'id'            => $this->id,
			'user_id'       => $this->user_id,
			'created_by_id' => $this->created_by_id,
			'created_at'    => $this->created_at,
			'updated_at'    => $this->updated_at,
			'deleted_at'    => $this->deleted_at,
		]);

		$query->andFilterWhere(['like', 'message', $this->message])
		      ->andFilterWhere(['like', 'created_by_type', $this->created_by_type]);

		return $dataProvider;
	}
}
