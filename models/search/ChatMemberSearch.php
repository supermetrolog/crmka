<?php

namespace app\models\search;

use app\exceptions\domain\model\ValidateException;
use app\kernel\common\models\Form\Form;
use app\models\ChatMember;
use yii\data\ActiveDataProvider;

class ChatMemberSearch extends Form
{
	public $id;
	public $model_type;
	public $model_id;
	public $created_at;
	public $updated_at;

	public function rules(): array
	{
		return [
			[['id', 'model_id'], 'integer'],
			[['model_type', 'created_at', 'updated_at'], 'safe'],
		];
	}

	/**
	 * TODO: add in generator template
	 *
	 * @return string
	 */
	public function formName(): string
	{
		return '';
	}

	/**
	 * @throws ValidateException
	 */
	public function search(array $params): ActiveDataProvider
	{
		$query = ChatMember::find()
		                   ->with(['objectChatMember.object.company'])
		                   ->with([
			                   'request.company',
			                   'request.regions',
			                   'request.directions',
			                   'request.districts',
			                   'request.objectTypes',
			                   'request.objectClasses',
		                   ])
		                   ->with(['user.userProfile']);

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
		]);

		$this->load($params);

		$this->validateOrThrow();

		$query->andFilterWhere([
			'id'         => $this->id,
			'model_id'   => $this->model_id,
			'model_type' => $this->model_type,
			'created_at' => $this->created_at,
			'updated_at' => $this->updated_at,
		]);

		return $dataProvider;
	}
}
