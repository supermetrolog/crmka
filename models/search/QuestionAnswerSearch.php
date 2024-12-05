<?php

namespace app\models\search;

use app\kernel\common\models\exceptions\ValidateException;
use app\kernel\common\models\Form\Form;
use app\models\QuestionAnswer;
use yii\data\ActiveDataProvider;

class QuestionAnswerSearch extends Form
{
	public $id;
	public $question_id;
	public $field_id;
	public $category;
	public $value;
	public $deleted;

	public function rules(): array
	{
		return [
			[['id', 'question_id', 'field_id'], 'integer'],
			['category', 'in', 'range' => QuestionAnswer::getCategories()],
			[['category', 'value'], 'safe'],
			[['deleted'], 'boolean'],
		];
	}

	/**
	 * @throws ValidateException
	 */
	public function search(array $params): ActiveDataProvider
	{
		$query = QuestionAnswer::find()->with('effects');

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
		]);

		$this->load($params);

		$this->validateOrThrow();

		$query->andFilterWhere([
			'id'          => $this->id,
			'question_id' => $this->question_id,
			'field_id'    => $this->field_id,
		]);

		$query->andFilterWhere(['like', 'value', $this->value]);
		$query->andFilterWhere(['like', 'category', $this->category]);

		if ($this->isFilterTrue($this->deleted)) {
			$query->deleted();
		}

		if ($this->isFilterFalse($this->deleted)) {
			$query->notDeleted();
		}

		return $dataProvider;
	}
}
