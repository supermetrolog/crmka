<?php

namespace app\models\search;

use app\kernel\common\models\exceptions\ValidateException;
use app\kernel\common\models\Form\Form;
use yii\data\ActiveDataProvider;
use app\models\Reminder;

class ReminderSearch extends Form
{
	public $id;
	public $user_id;
	public $message;
	public $status;
	public $created_by_type;
	public $created_by_id;
	public $notify_at;
	public $created_at;
	public $updated_at;
	public $deleted;
	
    public function rules(): array
    {
        return [
            [['id', 'user_id', 'status', 'created_by_id'], 'integer'],
            [['deleted'], 'boolean'],
            [['message', 'created_by_type', 'notify_at', 'created_at', 'updated_at'], 'safe'],
        ];
    }

	/**
	 * @throws ValidateException
	 */
    public function search(array $params): ActiveDataProvider
    {
        $query = Reminder::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

		$this->validateOrThrow();

		if ($this->isFilterTrue($this->deleted)) {
			$query->deleted();
		}

		if ($this->isFilterFalse($this->deleted)) {
			$query->notDeleted();
		}

        $query->andFilterWhere([
            'id' => $this->id,
            'user_id' => $this->user_id,
            'status' => $this->status,
            'created_by_id' => $this->created_by_id,
            'notify_at' => $this->notify_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'message', $this->message])
            ->andFilterWhere(['like', 'created_by_type', $this->created_by_type]);

        return $dataProvider;
    }
}
