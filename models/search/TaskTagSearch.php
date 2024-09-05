<?php

namespace app\models\search;

use app\kernel\common\models\exceptions\ValidateException;
use app\kernel\common\models\Form\Form;
use app\models\TaskTag;
use yii\data\ActiveDataProvider;

class TaskTagSearch extends Form
{
	public $id;
	public $name;
	public $description;
	public $color;
	public $created_at;
	public $updated_at;
	public $deleted_at;

	public $search;

	public function rules(): array
	{
		return [
			[['id'], 'integer'],
			[['name', 'description', 'color', 'created_at', 'updated_at', 'deleted_at', 'search'], 'safe'],
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
		$query = TaskTag::find()->notDeleted();

		$dataProvider = new ActiveDataProvider([
			'query'      => $query,
			'pagination' => false
		]);

		$this->load($params);

		$this->validateOrThrow();

		$query->andFilterWhere([
			'id'          => $this->id,
			'name'        => $this->name,
			'description' => $this->description,
			'color'       => $this->color,
			'created_at'  => $this->created_at,
			'updated_at'  => $this->updated_at,
			'deleted_at'  => $this->deleted_at,
		]);

		$query->andFilterWhere(['like', 'name', $this->name])
		      ->andFilterWhere(['like', 'description', $this->description])
		      ->andFilterWhere(['like', 'color', $this->color]);

		if (!isset($this->search)) {
			$query->andFilterWhere([
				'or',
				['like', 'name', $this->search],
				['like', 'description', $this->search]
			]);
		}

		return $dataProvider;
	}
}
