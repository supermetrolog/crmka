<?php

namespace app\models\search;

use app\kernel\common\models\exceptions\ValidateException;
use app\kernel\common\models\Form\Form;
use yii\data\ActiveDataProvider;
use app\models\Field;

class FieldSearch extends Form
{
	public $id;
	public $field_type;
	public $type;
	public $created_at;
	public $updated_at;
	public $deleted_at;
	
	public function rules(): array
	{
		return [
			[['id', 'field_type', 'type'], 'integer'],
			[['created_at', 'updated_at', 'deleted_at'], 'safe'],
		];
	}

	/**
	 * @throws ValidateException
	 */
	public function search(array $params): ActiveDataProvider
	{
		$query = Field::find();

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
		]);

		$this->load($params);

		$this->validateOrThrow();

		$query->andFilterWhere([
			'id'         => $this->id,
			'field_type' => $this->field_type,
			'type'       => $this->type,
			'created_at' => $this->created_at,
			'updated_at' => $this->updated_at,
			'deleted_at' => $this->deleted_at,
		]);

		return $dataProvider;
	}
}
