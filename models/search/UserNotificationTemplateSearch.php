<?php

namespace app\models\search;

use app\kernel\common\models\exceptions\ValidateException;
use app\kernel\common\models\Form\Form;
use app\models\Notification\UserNotificationTemplate;
use yii\base\ErrorException;
use yii\data\ActiveDataProvider;

class UserNotificationTemplateSearch extends Form
{
	public $id;
	public $priority;
	public $category;
	public $kind;
	public $is_active;

	public function rules(): array
	{
		return [
			[['id'], 'integer'],
			[['priority', 'category', 'kind'], 'string'],
			['is_active', 'boolean']
		];
	}

	/**
	 * @throws ValidateException
	 * @throws ErrorException
	 */
	public function search(array $params): ActiveDataProvider
	{
		$query = UserNotificationTemplate::find()
		                                 ->distinct();

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
			'sort'  => [
				'defaultOrder' => [
					'created_at' => SORT_DESC,
				],
				'attributes'   => [
					'created_at'
				]
			]
		]);

		$this->load($params);

		$this->validateOrThrow();

		$query->andFilterWhere([
			UserNotificationTemplate::field('id')        => $this->id,
			UserNotificationTemplate::field('is_active') => $this->is_active
		]);

		$query->andFilterWhere(['like', UserNotificationTemplate::field('kind'), $this->kind])
		      ->andFilterWhere(['like', UserNotificationTemplate::field('priority'), $this->priority])
		      ->andFilterWhere(['like', UserNotificationTemplate::field('category'), $this->category]);

		return $dataProvider;
	}

}
