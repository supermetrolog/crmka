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
	public $deleted;
	
	public function rules(): array
	{
		return [
			[['id', 'field_type', 'type'], 'integer'],
			[['deleted'], 'boolean'],
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
		]);

		if ($this->isFilterTrue($this->deleted)) {
			$query->deleted();
		}

		if ($this->isFilterFalse($this->deleted)) {
			$query->notDeleted();
		}
		
		return $dataProvider;
	}
}
