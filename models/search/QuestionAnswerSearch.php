<?php

namespace app\models\search;

use app\kernel\common\models\exceptions\ValidateException;
use app\kernel\common\models\Form\Form;
use yii\data\ActiveDataProvider;
use app\models\QuestionAnswer;

class QuestionAnswerSearch extends Form
{
	public $id;
	public $field_id;
	public $category;
	public $value;
	public $deleted;

	public function rules(): array
	{
		return [
			[['id', 'field_id', 'category'], 'integer'],
			[['value'], 'safe'],
			[['deleted'], 'boolean'],
		];
	}

	/**
	 * @throws ValidateException
	 */
	public function search(array $params): ActiveDataProvider
	{
		$query = QuestionAnswer::find();

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
		]);

		$this->load($params);

		$this->validateOrThrow();

		$query->andFilterWhere([
			'id'       => $this->id,
			'field_id' => $this->field_id,
			'category' => $this->category,
		]);

		$query->andFilterWhere(['like', 'value', $this->value]);

		if ($this->isFilterTrue($this->deleted)) {
			$query->deleted();
		}

		if ($this->isFilterFalse($this->deleted)) {
			$query->notDeleted();
		}

		return $dataProvider;
	}
}
