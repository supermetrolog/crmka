<?php

namespace app\models\search;

use app\kernel\common\models\exceptions\ValidateException;
use app\kernel\common\models\Form\Form;
use app\models\TaskComment;
use yii\data\ActiveDataProvider;

class TaskCommentSearch extends Form
{
	public $id;
	public $created_by_id;
	public $task_id;


	public function rules(): array
	{
		return [
			[['id', 'user_id', 'task_id', 'created_by_id'], 'integer']
		];
	}

	/**
	 * @throws ValidateException
	 */
	public function search(array $params): ActiveDataProvider
	{
		$query = TaskComment::find()->with('createdBy.userProfile');

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
		]);

		$this->load($params);
		$this->validateOrThrow();

		$query->andFilterWhere([
			'id'            => $this->id,
			'task_id'       => $this->task_id,
			'created_by_id' => $this->created_by_id,
		]);

		return $dataProvider;
	}
}
