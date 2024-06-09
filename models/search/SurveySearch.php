<?php

namespace app\models\search;

use app\kernel\common\models\exceptions\ValidateException;
use app\kernel\common\models\Form\Form;
use yii\data\ActiveDataProvider;
use app\models\Survey;

class SurveySearch extends Form
{
	public $id;
	public $user_id;
	public $contact_id;

	public function rules(): array
	{
		return [
			[['id', 'user_id', 'contact_id'], 'integer'],
		];
	}

	/**
	 * @throws ValidateException
	 */
	public function search(array $params): ActiveDataProvider
	{
		$query = Survey::find();

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
		]);

		$this->load($params);

		$this->validateOrThrow();

		$query->andFilterWhere([
			'id'         => $this->id,
			'user_id'    => $this->user_id,
			'contact_id' => $this->contact_id,
		]);

		return $dataProvider;
	}
}
