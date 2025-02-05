<?php

namespace app\models\search;

use app\kernel\common\models\exceptions\ValidateException;
use app\kernel\common\models\Form\Form;
use app\models\Question;
use app\models\QuestionAnswer;
use yii\base\ErrorException;
use yii\data\ActiveDataProvider;

class QuestionAnswerSearch extends Form
{
	public $id;
	public $question_id;
	public $field_id;
	public $category;
	public $value;
	public $deleted;

	public $search;
	public $has_effects = false;


	public $with_questions = false;

	public function rules(): array
	{
		return [
			[['id', 'question_id', 'field_id'], 'integer'],
			['category', 'in', 'range' => QuestionAnswer::getCategories()],
			[['category', 'value', 'search'], 'safe'],
			[['deleted', 'has_effects'], 'boolean'],
		];
	}

	/**
	 * @throws ValidateException
	 * @throws ErrorException
	 */
	public function search(array $params): ActiveDataProvider
	{
		$query = QuestionAnswer::find();

		$dataProvider = new ActiveDataProvider([
			'query'      => $query,
			'pagination' => [
				'defaultPageSize' => 20,
				'pageSizeLimit'   => [0, 50],
			],
			'sort'       => [
				'defaultOrder' => [
					'id' => SORT_DESC
				],
				'attributes'   => ['id', 'created_at', 'updated_at', 'question_id'],
			]
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

		if ($this->isFilterTrue($this->has_effects)) {
			$query->innerJoinWith(['effects']);
		} else {
			$query->with(['effects']);
		}

		if ($this->isFilterTrue($this->with_questions)) {
			$query->with(['question']);
		}

		if (!empty($this->search)) {
			$query->joinWith(['question']);

			$query->andFilterWhere([
				'or',
				['like', QuestionAnswer::field('value'), $this->search],
				['like', QuestionAnswer::field('id'), $this->search],
				['like', Question::field('text'), $this->search],
				['like', Question::field('id'), $this->search],
			]);
		}

		return $dataProvider;
	}
}
