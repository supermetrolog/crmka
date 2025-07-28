<?php

namespace app\models\search;

use app\enum\EntityMessageLink\EntityMessageLinkKindEnum;
use app\helpers\SQLHelper;
use app\helpers\validators\EnumValidator;
use app\kernel\common\models\exceptions\ValidateException;
use app\kernel\common\models\Form\Form;
use app\models\ChatMemberMessage;
use app\models\EntityMessageLink;
use app\models\UserProfile;
use yii\base\ErrorException;
use yii\data\ActiveDataProvider;

class EntityMessageLinkSearch extends Form
{
	public $id;
	public $created_by_id;
	public $created_by_ids = [];
	public $kind;
	public $entity_type;
	public $entity_id;

	public $search;

	public function rules(): array
	{
		return [
			[['id', 'created_by_id', 'entity_id'], 'integer'],
			[['search', 'kind', 'entity_type'], 'string'],
			['kind', EnumValidator::class, 'enumClass' => EntityMessageLinkKindEnum::class],
			[['created_by_ids'], 'each', 'rule' => ['integer']],
		];
	}

	/**
	 * @throws ValidateException
	 * @throws ErrorException
	 */
	public function search(array $params): ActiveDataProvider
	{
		$query = EntityMessageLink::find()
		                          ->joinWith(['chatMemberMessage.fromChatMember.user.userProfile', 'chatMemberMessage.files']);

		$dataProvider = new ActiveDataProvider([
			'query'      => $query,
			'pagination' => [
				'defaultPageSize' => 10,
				'pageSizeLimit'   => [0, 30],
			],
			'sort'       => [
				'defaultOrder' => [
					'created_at' => SORT_DESC
				],
				'attributes'   => [
					'created_at'
				]
			]
		]);

		$this->load($params);

		$this->validateOrThrow();

		$query->andFilterWhere([
			EntityMessageLink::field('id')            => $this->id,
			EntityMessageLink::field('created_by_id') => $this->created_by_id,
			EntityMessageLink::field('kind')          => $this->kind,
			EntityMessageLink::field('entity_type')   => $this->entity_type,
			EntityMessageLink::field('entity_id')     => $this->entity_id
		]);

		$query->andFilterWhere([
			EntityMessageLink::field('created_by_id') => $this->created_by_ids,
		]);

		if ($this->hasFilter($this->search)) {
			$query->andFilterWhere([
				'or',
				['like', EntityMessageLink::field('id'), $this->search],
				[
					'like',
					SQLHelper::concatWithCoalesce([
						UserProfile::field('first_name'),
						UserProfile::field('middle_name'),
						UserProfile::field('last_name')
					]),
					$this->search
				],
				['like', ChatMemberMessage::field('message'), $this->search]
			]);
		}

		return $dataProvider;
	}
}