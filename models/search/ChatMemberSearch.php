<?php

namespace app\models\search;

use app\helpers\DumpHelper;
use app\kernel\common\models\exceptions\ValidateException;
use app\kernel\common\models\Form\Form;
use app\models\ChatMember;
use app\models\ChatMemberMessage;
use app\models\ObjectChatMember;
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
	public $object_id;

	public function rules(): array
	{
		return [
			[['id', 'model_id', 'company_id', 'object_id'], 'integer'],
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
		$subQuery = ChatMemberMessage::find()
		                             ->select([
			                             'from_chat_member_id', 'created_at' => 'MAX(created_at)',
		                             ])
		                             ->groupBy('from_chat_member_id');

		$query = ChatMember::find()
		                   ->select([
			                   ChatMember::getColumn('*'),
			                   'last_call_rel_id' => 'last_call_rel.id'
		                   ])
		                   ->leftJoinLastCallRelation()
		                   ->leftJoin(['cmm' => $subQuery], ChatMember::getColumn('id') . '=' . 'cmm.from_chat_member_id')
		                   ->joinWith([
			                   'objectChatMember.object',
			                   'request'
		                   ])
		                   ->with(['lastCall.user.userProfile'])
		                   ->with(['objectChatMember.object.company'])
		                   ->with([
			                   'request.company',
			                   'request.regions',
			                   'request.directions',
			                   'request.districts',
			                   'request.objectTypes',
			                   'request.objectClasses',
		                   ])
		                   ->with(['user.userProfile'])
		                   ->orderBy(['cmm.created_at' => SORT_DESC]);

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
		]);

		$this->load($params);

		$this->validateOrThrow();


		$query->orFilterWhere([Request::field('company_id') => $this->company_id])
		      ->orFilterWhere([Objects::field('company_id') => $this->company_id]);

		$query->andFilterWhere([
			ChatMember::field('id')              => $this->id,
			ChatMember::field('model_id')        => $this->model_id,
			ChatMember::field('model_type')      => $this->model_type,
			ChatMember::field('created_at')      => $this->created_at,
			ChatMember::field('updated_at')      => $this->updated_at,
			ObjectChatMember::field('object_id') => $this->object_id
		]);


		return $dataProvider;
	}
}
