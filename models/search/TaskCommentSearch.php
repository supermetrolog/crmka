<?php

namespace app\models\search;

use app\kernel\common\models\exceptions\ValidateException;
use app\kernel\common\models\Form\Form;
use app\models\TaskComment;
use yii\base\ErrorException;
use yii\data\ActiveDataProvider;

class TaskCommentSearch extends Form
{
	public $id;
	public $created_by_id;
	public $task_id;
	public $user_id;

	public $id_less_then;

	public $limit = 10;


	public function rules(): array
	{
		return [
			[['id', 'user_id', 'task_id', 'created_by_id', 'id_less_then'], 'integer'],
			['limit', 'integer', 'max' => 100, 'min' => 5]
		];
	}

	/**
	 * @throws ValidateException
	 * @throws ErrorException
	 */
	public function search(array $params): ActiveDataProvider
	{
		$query = TaskComment::find()->with('createdBy.userProfile')
		                    ->notDeleted()
		                    ->limit($this->limit)
		                    ->orderBy([TaskComment::field('id') => SORT_DESC]);

		$dataProvider = new ActiveDataProvider([
			'query'      => $query,
			'pagination' => false
		]);

		$this->load($params);
		$this->validateOrThrow();

		$query->andFilterWhere([
			'id'            => $this->id,
			'task_id'       => $this->task_id,
			'created_by_id' => $this->created_by_id,
			'user_id'       => $this->user_id
		]);

		$query->andFilterWhere(['<', TaskComment::field('id'), $this->id_less_then]);

		return $dataProvider;
	}
}
