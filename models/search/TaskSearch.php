<?php

namespace app\models\search;

use app\exceptions\domain\model\ValidateException;
use app\kernel\common\models\Form;
use yii\data\ActiveDataProvider;
use app\models\Task;

class TaskSearch extends Form
{
	public $id;
	public $user_id;
	public $message;
	public $status;
	public $start;
	public $end;
	public $created_by_type;
	public $created_by_id;
	public $created_at;
	public $updated_at;
	public $deleted_at;
	
    public function rules(): array
    {
        return [
            [['id', 'user_id', 'status', 'created_by_id'], 'integer'],
            [['message', 'start', 'end', 'created_by_type', 'created_at', 'updated_at', 'deleted_at'], 'safe'],
        ];
    }

	/**
	 * @throws ValidateException
	 */
    public function search(array $params): ActiveDataProvider
    {
        $query = Task::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

		$this->validateOrThrow();

        $query->andFilterWhere([
            'id' => $this->id,
            'user_id' => $this->user_id,
            'status' => $this->status,
            'start' => $this->start,
            'end' => $this->end,
            'created_by_id' => $this->created_by_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
        ]);

        $query->andFilterWhere(['like', 'message', $this->message])
            ->andFilterWhere(['like', 'created_by_type', $this->created_by_type]);

        return $dataProvider;
    }
}
