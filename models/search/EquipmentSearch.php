<?php

namespace app\models\search;

use app\kernel\common\models\exceptions\ValidateException;
use app\kernel\common\models\Form\Form;
use app\models\Equipment;
use yii\data\ActiveDataProvider;
use yii\db\Expression;

class EquipmentSearch extends Form
{
	public $id;
	public $name;
	public $address;
	public $description;
	public $company_id;
	public $contact_id;
	public $consultant_id;
	public $preview_id;
	public $category;
	public $availability;
	public $delivery;
	public $deliveryPrice;
	public $price;
	public $benefit;
	public $tax;
	public $count;
	public $state;
	public $status;
	public $passive_type;
	public $passive_comment;
	public $archived_at;
	public $created_by_type;
	public $created_by_id;
	public $created_at;
	public $updated_at;
	public $deleted_at;

	public $search;

	public $minPrice;
	public $maxPrice;
	public $minCount;
	public $maxCount;

	public function rules(): array
	{
		return [
			[['id', 'company_id', 'contact_id', 'preview_id', 'availability', 'deliveryPrice', 'price', 'benefit', 'tax', 'count', 'state', 'status', 'passive_type', 'created_by_id', 'minCount', 'maxCount', 'minPrice', 'maxPrice'], 'integer'],
			['category', 'each', 'rule' => ['integer']],
			['delivery', 'each', 'rule' => ['integer']],
			['consultant_id', 'each', 'rule' => ['integer']],
			[['name', 'address', 'description', 'passive_comment', 'archived_at', 'created_by_type', 'created_at', 'updated_at', 'deleted_at', 'search'], 'safe'],
		];
	}

	/**
	 * @throws ValidateException
	 */
	public function search(array $params): ActiveDataProvider
	{
		$query = Equipment::find()
		                  ->select([
			                  Equipment::field('*'),
			                  'last_call_rel_id' => 'last_call_rel.id'
		                  ])
		                  ->leftJoinLastCallRelation()
		                  ->with(['lastCall.user.userProfile'])
		                  ->with([
			                  'company',
			                  'contact',
			                  'consultant.userProfile',
			                  'preview',
			                  'files',
			                  'photos',
		                  ]);

		$dataProvider = new ActiveDataProvider([
			'query'        => $query,
			'sort' => [
				'attributes'   => [
					'created_at',
					'updated_at',
					'archived_at',
					'price',
					'count',
				],
			],
		]);

		$this->load($params);

		$this->validateOrThrow();

		$query->andFilterWhere([
			'id'            => $this->id,
			'company_id'    => $this->company_id,
			'contact_id'    => $this->contact_id,
			'consultant_id' => $this->consultant_id,
			'preview_id'    => $this->preview_id,
			'category'      => $this->category,
			'availability'  => $this->availability,
			'delivery'      => $this->delivery,
			'deliveryPrice' => $this->deliveryPrice,
			'price'         => $this->price,
			'benefit'       => $this->benefit,
			'tax'           => $this->tax,
			'count'         => $this->count,
			'state'         => $this->state,
			'status'        => $this->status,
			'passive_type'  => $this->passive_type,
			'archived_at'   => $this->archived_at,
			'created_by_id' => $this->created_by_id,
			'created_at'    => $this->created_at,
			'updated_at'    => $this->updated_at,
			'deleted_at'    => $this->deleted_at,
		]);

		$query->andFilterWhere(['>', 'price', $this->minPrice])
		      ->andFilterWhere(['<', 'price', $this->maxPrice])
		      ->andFilterWhere(['>', 'count', $this->minCount])
		      ->andFilterWhere(['<', 'count', $this->maxCount]);

		$query->andFilterWhere(['like', 'name', $this->name])
		      ->andFilterWhere(['like', 'address', $this->address])
		      ->andFilterWhere(['like', 'description', $this->description])
		      ->andFilterWhere(['like', 'passive_comment', $this->passive_comment])
		      ->andFilterWhere(['like', 'created_by_type', $this->created_by_type]);

		$query->orFilterWhere(['like', 'name', $this->search])
		      ->orFilterWhere(['like', 'address', $this->search])
		      ->orFilterWhere(['like', 'description', $this->search]);

		return $dataProvider;
	}
}
