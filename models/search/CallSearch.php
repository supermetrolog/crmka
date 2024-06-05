<?php

namespace app\models\search;

use app\kernel\common\models\exceptions\ValidateException;
use app\kernel\common\models\Form\Form;
use yii\data\ActiveDataProvider;
use app\models\Call;

class CallSearch extends Form
{
	public $id;
	public $user_id;
	public $contact_id;
	public $created_at;
	public $updated_at;
	public $deleted_at;
	
    public function rules(): array
    {
        return [
            [['id', 'user_id', 'contact_id'], 'integer'],
            [['created_at', 'updated_at', 'deleted_at'], 'safe'],
        ];
    }

	/**
	 * @throws ValidateException
	 */
    public function search(array $params): ActiveDataProvider
    {
        $query = Call::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

		$this->validateOrThrow();

        $query->andFilterWhere([
            'id' => $this->id,
            'user_id' => $this->user_id,
            'contact_id' => $this->contact_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
        ]);

        return $dataProvider;
    }
}
