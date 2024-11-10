<?php

namespace app\models\search;

use app\kernel\common\models\exceptions\ValidateException;
use app\kernel\common\models\Form\Form;
use app\models\Effect;
use yii\base\ErrorException;
use yii\data\ActiveDataProvider;

class EffectSearch extends Form
{
	public $id;
	public $title;
	public $description;
	public $kind;

	public $query;

	public function rules(): array
	{
		return [
			[['id'], 'integer'],
			[['title', 'description', 'kind'], 'string'],
		];
	}

	/**
	 * @throws ValidateException
	 * @throws ErrorException
	 */
	public function search(array $params): ActiveDataProvider
	{
		$query = Effect::find();

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
		]);

		$this->load($params);

		$this->validateOrThrow();

		$query->andFilterWhere([
			'id' => $this->id
		]);

		$query->andFilterWhere(['like', Effect::field('like'), $this->title]);
		$query->andFilterWhere(['like', Effect::field('description'), $this->description]);
		$query->andFilterWhere(['like', Effect::field('kind'), $this->kind]);

		$query->andFilterWhere(['like', Effect::field('title'), $this->query]);
		$query->andFilterWhere(['like', Effect::field('description'), $this->query]);
		$query->andFilterWhere(['like', Effect::field('kind'), $this->query]);

		return $dataProvider;
	}
}
