<?php

namespace app\models\search;

use app\kernel\common\models\exceptions\ValidateException;
use app\kernel\common\models\Form\Form;
use app\models\TaskObserver;
use yii\data\ActiveDataProvider;

class TaskObserverSearch extends Form
{
	public $id;
	public $user_id;
	public $created_by_id;
	public $task_id;
	public $viewed;


	public function rules(): array
	{
		return [
			[['id', 'user_id', 'task_id', 'created_by_id'], 'integer'],
			[['viewed'], 'boolean'],
		];
	}

	/**
	 * @throws ValidateException
	 */
	public function search(array $params): ActiveDataProvider
	{
		$query = TaskObserver::find()->with('user.userProfile');

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
		]);

		$this->load($params);

		$this->validateOrThrow();

		if ($this->isFilterTrue($this->viewed)) {
			$query->viewed();
		}

		if ($this->isFilterFalse($this->viewed)) {
			$query->notViewed();
		}

		$query->andFilterWhere([
			'id'            => $this->id,
			'user_id'       => $this->user_id,
			'task_id'       => $this->task_id,
			'created_by_id' => $this->created_by_id,
		]);

		return $dataProvider;
	}
}
