<?php

namespace app\models\search;

use app\kernel\common\models\exceptions\ValidateException;
use app\kernel\common\models\Form\Form;
use app\models\ChatMember;
use app\models\Objects;
use app\models\Request;
use yii\base\ErrorException;
use yii\data\ActiveDataProvider;

class ChatMemberSearch extends Form
{
	public $id;
	public $model_type;
	public $model_id;
	public $created_at;
	public $updated_at;

	public $company_id;

	public function rules(): array
	{
		return [
			[['id', 'model_id', 'company_id'], 'integer'],
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
	 * @throws ErrorException
	 */
	public function search(array $params): ActiveDataProvider
	{
		$query = ChatMember::find()
		                   ->joinWith([
			                   'objectChatMember.object',
			                   'request'
		                   ])
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
			ChatMember::field('id')         => $this->id,
			ChatMember::field('model_id')   => $this->model_id,
			ChatMember::field('model_type') => $this->model_type,
			ChatMember::field('created_at') => $this->created_at,
			ChatMember::field('updated_at') => $this->updated_at,
		]);

		$query->orFilterWhere([Request::field('company_id') => $this->company_id])
		      ->orFilterWhere([Objects::field('company_id') => $this->company_id]);

		return $dataProvider;
	}
}
