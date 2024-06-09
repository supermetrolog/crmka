<?php

namespace app\models\search;

use app\kernel\common\models\exceptions\ValidateException;
use app\kernel\common\models\Form\Form;
use app\models\SurveyQuestionAnswer;
use yii\data\ActiveDataProvider;

class SurveyQuestionAnswerSearch extends Form
{
	public $id;
	public $survey_id;
	public $question_answer_id;
	public $value;

	public function rules(): array
	{
		return [
			[['id', 'survey_id', 'question_answer_id'], 'integer'],
			[['value'], 'safe'],
		];
	}

	/**
	 * @throws ValidateException
	 */
	public function search(array $params): ActiveDataProvider
	{
		$query = SurveyQuestionAnswer::find();

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
		]);

		$this->load($params);

		$this->validateOrThrow();

		$query->andFilterWhere([
			'id'                 => $this->id,
			'survey_id'          => $this->survey_id,
			'question_answer_id' => $this->question_answer_id,
		]);

		$query->andFilterWhere(['like', 'value', $this->value]);

		return $dataProvider;
	}
}
