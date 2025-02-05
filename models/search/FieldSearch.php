<?php

namespace app\models\search;

use app\kernel\common\models\exceptions\ValidateException;
use app\kernel\common\models\Form\Form;
use app\models\Field;
use yii\data\ActiveDataProvider;

class FieldSearch extends Form
{
	public $id;
	public $field_type;
	public $type;
	public $deleted;

	public function rules(): array
	{
		return [
			[['id'], 'integer'],
			[['field_type', 'type'], 'safe'],
			['field_type', 'in', 'range' => Field::getFieldTypes()],
			['type', 'in', 'range' => Field::getTypes()],
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
			'query'      => $query,
			'pagination' => [
				'defaultPageSize' => 20,
				'pageSizeLimit'   => [0, 50],
			]
		]);

		$this->load($params);

		$this->validateOrThrow();

		$query->andFilterWhere([
			'id' => $this->id,
		]);

		$query->andFilterWhere(['like', 'field_type', $this->field_type]);
		$query->andFilterWhere(['like', 'type', $this->type]);

		if ($this->isFilterTrue($this->deleted)) {
			$query->deleted();
		}

		if ($this->isFilterFalse($this->deleted)) {
			$query->notDeleted();
		}

		return $dataProvider;
	}
}
