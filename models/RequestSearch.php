<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Request;
use yii\db\Expression;

/**
 * RequestSearch represents the model behind the search form of `app\models\Request`.
 */
class RequestSearch extends Request
{
	public $all;
	public $dateStart;
	public $dateEnd;
	public $objectTypes;
	public $objectTypesGeneral;
	public $objectClasses;
	public $gateTypes;
	public $rangeMinPricePerFloor;
	public $rangeMaxPricePerFloor;
	public $rangeMinArea;
	public $rangeMaxArea;
	public $rangeMinCeilingHeight;
	public $rangeMaxCeilingHeight;
	public $maxDistanceFromMKAD;
	public $maxElectricity;
	public $regions;
	public $directions;
	public $districts;

	/**
	 * {@inheritdoc}
	 */
	public function rules()
	{
		return [
			[['region_neardy', 'outside_mkad', 'id', 'company_id', 'dealType', 'expressRequest', 'distanceFromMKAD', 'distanceFromMKADnotApplicable', 'minArea', 'maxArea', 'minCeilingHeight', 'maxCeilingHeight', 'firstFloorOnly', 'heated', 'trainLine', 'trainLineLength', 'consultant_id', 'pricePerFloor', 'electricity', 'haveCranes', 'unknownMovingDate', 'antiDustOnly', 'passive_why', 'rangeMinPricePerFloor', 'rangeMaxPricePerFloor', 'rangeMinArea', 'rangeMaxArea', 'rangeMinCeilingHeight', 'rangeMaxCeilingHeight', 'maxDistanceFromMKAD', 'water', 'sewerage', 'gaz', 'steam', 'shelving', 'maxElectricity'], 'integer'],
			[['status', 'regions', 'directions', 'districts', 'description', 'created_at', 'updated_at', 'movingDate', 'passive_why_comment', 'all', 'dateStart', 'dateEnd', 'objectTypes', 'objectTypesGeneral', 'objectClasses', 'gateTypes'], 'safe'],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function scenarios()
	{
		// bypass scenarios() implementation in the parent class
		return Model::scenarios();
	}

	public function stringToArray($value)
	{
		if (is_string($value)) {
			return explode(",", $value);
		}

		return $value;
	}

	public function normalizeProps()
	{
		$this->objectTypes        = $this->stringToArray($this->objectTypes);
		$this->objectTypesGeneral = $this->stringToArray($this->objectTypesGeneral);
		$this->objectClasses      = $this->stringToArray($this->objectClasses);
		$this->gateTypes          = $this->stringToArray($this->gateTypes);
		$this->regions            = $this->stringToArray($this->regions);
		$this->directions         = $this->stringToArray($this->directions);
		$this->districts          = $this->stringToArray($this->districts);
		$this->status             = $this->stringToArray($this->status);
		if ($this->dateStart === null && $this->dateEnd === null) {
			return;
		}

		$this->dateStart = $this->dateStart ?? date('Y-m-d', strtotime('01.01.1970'));
		$this->dateEnd   = $this->dateEnd ?? date('Y-m-d');
	}

	/**
	 * Creates data provider instance with search query applied
	 *
	 * @param array $params
	 *
	 * @return ActiveDataProvider
	 */
	public function search($params)
	{
		$query = Request::find()->distinct()->joinWith(['objectTypesGeneral', 'objectTypes', 'objectClasses', 'gateTypes', 'company', 'directions', 'districts', 'regions'])->with(['consultant.userProfile', 'directions', 'districts', 'regions.info', 'deal.company', 'deal.offer.generalOffersMix', 'deal.consultant.userProfile', 'contact.emails', 'contact.phones']);

		// add conditions that should always apply here

		$dataProvider = new ActiveDataProvider([
			'query'      => $query,
			'pagination' => [
				'pageSize' => 50
			],
			'sort'       => [
				'enableMultiSort' => true,
				'defaultOrder'    => [
					'default' => SORT_DESC
				],
				'attributes'      => [
					'pricePerFloor',
					'related_updated_at',
					'created_at',
					'updated_at',
					'dealType' => [
						'asc'  => [
							new Expression('FIELD(request.dealType, 3,1,2,0) ASC')
						],
						'desc' => [
							new Expression('FIELD(request.dealType, 3,1,2,0) DESC')
						]
					],
					'status'   => [
						'asc'  => [
							new Expression('FIELD(request.status, 2,0,1) ASC'),
						],
						'desc' => [
							new Expression('FIELD(request.status, 2,0,1) DESC'),
						],
					],
					'default'  => [
						'asc'  => [
							new Expression('FIELD(request.status, 2,0,1) ASC'),
							'request.expressRequest' => SORT_ASC,
							'request.updated_at'     => SORT_ASC
						],
						'desc' => [
							new Expression('FIELD(request.status, 2,0,1) DESC'),
							'request.expressRequest' => SORT_DESC,
							'request.updated_at'     => SORT_DESC
						],
					]
				]
			]
		]);

		$this->load($params, '');
		$this->normalizeProps();

		if (!$this->validate()) {
			// uncomment the following line if you do not want to return any records when validation fails
			// $query->where('0=1');
			return $dataProvider;
		}

		$query->orFilterWhere(['request.id' => $this->all])
		      ->orFilterWhere(['like', 'company.nameEng', $this->all])
		      ->orFilterWhere(['like', 'company.nameRu', $this->all]);

		// grid filtering conditions
		$query->andFilterWhere([
			'request.id'                            => $this->id,
			'request.company_id'                    => $this->company_id,
			'request.dealType'                      => $this->dealType,
			'request.expressRequest'                => $this->expressRequest,
			'request.distanceFromMKAD'              => $this->distanceFromMKAD,
			'request.distanceFromMKADnotApplicable' => $this->distanceFromMKADnotApplicable,
			'request.minCeilingHeight'              => $this->minCeilingHeight,
			'request.maxCeilingHeight'              => $this->maxCeilingHeight,
			'request.firstFloorOnly'                => $this->firstFloorOnly,
			'request.outside_mkad'                  => $this->outside_mkad,
			// 'request.heated' => $this->heated,
			'request.minArea'                       => $this->minArea,
			'request.maxArea'                       => $this->maxArea,
			'request.trainLine'                     => $this->trainLine,
			'request.trainLineLength'               => $this->trainLineLength,
			'request.consultant_id'                 => $this->consultant_id,
			'request.pricePerFloor'                 => $this->pricePerFloor,
			'request.electricity'                   => $this->electricity,
			'request.haveCranes'                    => $this->haveCranes,
			'request.status'                        => $this->status,
			'request.created_at'                    => $this->created_at,
			'request.updated_at'                    => $this->updated_at,
			'request.movingDate'                    => $this->movingDate,
			'request.unknownMovingDate'             => $this->unknownMovingDate,
			'request.antiDustOnly'                  => $this->antiDustOnly,
			'request.passive_why'                   => $this->passive_why,
			'request.water'                         => $this->water,
			'request.steam'                         => $this->steam,
			'request.sewerage'                      => $this->sewerage,
			'request.gaz'                           => $this->gaz,
			'request.shelving'                      => $this->shelving,
			'request_object_type.object_type'       => $this->objectTypes,
			'request_object_type_general.type'      => $this->objectTypesGeneral,
			'request_object_class.object_class'     => $this->objectClasses,
			'request_gate_type.gate_type'           => $this->gateTypes,
			'request_direction.direction'           => $this->directions,
			'request_district.district'             => $this->districts,
		]);

		if ($this->heated !== null) {
			$query->andFilterWhere([
				'or',
				['heated' => $this->heated],
				['is', 'heated', new Expression('null')]
			]);
		}

		if ($this->region_neardy) {
			/**
			 *  TODO: узнать каике регионы являются ближайшими к МО
			 */
			$query->andFilterWhere(['!=', 'request_region.region', 0]);
		} else {
			$query->andFilterWhere(['request_region.region' => $this->regions]);
		}

		$query->andFilterWhere(['like', 'request.description', $this->description])
		      ->andFilterWhere(['like', 'request.passive_why_comment', $this->passive_why_comment])
		      ->andFilterWhere(['between', 'request.created_at', $this->dateStart, $this->dateEnd])
		      ->andFilterWhere(['<=', 'request.maxArea', $this->rangeMaxArea])
		      ->andFilterWhere(['>=', 'request.maxArea', $this->rangeMinArea])
		      ->andFilterWhere(['<=', 'request.pricePerFloor', $this->rangeMaxPricePerFloor])
		      ->andFilterWhere(['>=', 'request.pricePerFloor', $this->rangeMinPricePerFloor])
		      ->andFilterWhere(['<=', 'request.maxCeilingHeight', $this->rangeMaxCeilingHeight])
		      ->andFilterWhere(['>=', 'request.maxCeilingHeight', $this->rangeMinCeilingHeight])
		      ->andFilterWhere(['<=', 'request.distanceFromMKAD', $this->maxDistanceFromMKAD])
		      ->andFilterWhere(['<=', 'request.electricity', $this->maxElectricity]);

		return $dataProvider;
	}
}
