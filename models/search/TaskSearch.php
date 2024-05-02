<?php

namespace app\models\search;

use app\exceptions\domain\model\ValidateException;
use app\kernel\common\models\Form\Form;
use app\models\Task;
use yii\data\ActiveDataProvider;

class TaskSearch extends Form
{
	public $id;
	public $user_id;
	public $message;
	public $status;
	public $created_by_id;
	public $deleted;
	public $expired;


	public function rules(): array
	{
		return [
			[['id', 'user_id', 'status', 'created_by_id'], 'integer'],
			[['deleted', 'expired'], 'boolean'],
			[['message', 'start', 'end', 'created_by_type'], 'safe'],
		];
	}

	/**
	 * @throws ValidateException
	 */
	public function search(array $params): ActiveDataProvider
	{
		$query = Task::find()->with(['user', 'createdByUser']);

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
		]);

		$this->load($params);

		$this->validateOrThrow();

		if ($this->deleted) {
			$query->deleted();
		}

		if ($this->expired) {
			$query->expired();
		}

		$query->andFilterWhere([
			'id'            => $this->id,
			'user_id'       => $this->user_id,
			'status'        => $this->status,
			'created_by_id' => $this->created_by_id,
		]);

		$query->andFilterWhere(['like', 'message', $this->message]);

		return $dataProvider;
	}
}
