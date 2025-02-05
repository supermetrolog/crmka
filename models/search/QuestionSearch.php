<?php

namespace app\models\search;

use app\kernel\common\models\exceptions\ValidateException;
use app\kernel\common\models\Form\Form;
use app\models\Question;
use yii\data\ActiveDataProvider;

class QuestionSearch extends Form
{
	public $id;
	public $text;
	public $deleted;
	public $group;

	public function rules(): array
	{
		return [
			[['id'], 'integer'],
			[['text'], 'safe'],
			[['deleted'], 'boolean'],
			[['group'], 'string'],
		];
	}

	/**
	 * @throws ValidateException
	 */
	public function search(array $params): ActiveDataProvider
	{
		$query = Question::find()->with('answers');

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
			'id'    => $this->id,
			'group' => $this->group
		]);

		$query->andFilterWhere(['like', 'text', $this->text]);

		if ($this->isFilterTrue($this->deleted)) {
			$query->deleted();
		}

		if ($this->isFilterFalse($this->deleted)) {
			$query->notDeleted();
		}

		return $dataProvider;
	}
}
