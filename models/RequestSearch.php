<?php

namespace app\models;

use app\helpers\SQLHelper;
use app\kernel\common\models\exceptions\ValidateException;
use app\kernel\common\models\Form\Form;
use app\models\ActiveQuery\UserQuery;
use app\models\miniModels\RequestDirection;
use app\models\miniModels\RequestDistrict;
use app\models\miniModels\RequestGateType;
use app\models\miniModels\RequestObjectClass;
use app\models\miniModels\RequestObjectType;
use app\models\miniModels\RequestObjectTypeGeneral;
use app\models\miniModels\RequestRegion;
use yii\base\ErrorException;
use yii\data\ActiveDataProvider;
use yii\db\Expression;

/**
 * RequestSearch represents the model behind the search form of `app\models\Request`.
 */
class RequestSearch extends Form
{
	public $id;
	public $company_id;
	public $contact_id;
	public $consultant_id;
	public $dealType;
	public $minArea;
	public $maxArea;
	public $minCeilingHeight;
	public $maxCeilingHeight;
	public $distanceFromMKAD;
	public $pricePerFloor;
	public $trainLineLength;
	public $electricity;
	public $unknownMovingDate;
	public $outside_mkad;
	public $region_neardy;
	public $passive_why;
	public $distanceFromMKADnotApplicable;
	public $firstFloorOnly;
	public $expressRequest;
	public $heated;
	public $antiDustOnly;
	public $trainLine;
	public $haveCranes;
	public $water;
	public $sewerage;
	public $gaz;
	public $steam;
	public $shelving;
	public $name;
	public $description;
	public $passive_why_comment;
	public $status;
	public $movingDate;

	public $all;
	public $dateStart;
	public $dateEnd;

	public $objectTypes        = [];
	public $objectTypesGeneral = [];
	public $objectClasses      = [];
	public $gateTypes          = [];
	public $rangeMinPricePerFloor;
	public $rangeMaxPricePerFloor;
	public $rangeMinArea;
	public $rangeMaxArea;
	public $rangeMinCeilingHeight;
	public $rangeMaxCeilingHeight;
	public $maxDistanceFromMKAD;
	public $maxElectricity;
	public $regions            = [];
	public $directions         = [];
	public $districts          = [];

	public $consultant_ids          = [];
	public $with_passive_consultant = false;

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array
	{
		return [
			[['region_neardy', 'outside_mkad', 'id', 'company_id', 'dealType', 'expressRequest', 'distanceFromMKAD', 'distanceFromMKADnotApplicable', 'minArea', 'maxArea', 'minCeilingHeight', 'maxCeilingHeight', 'firstFloorOnly', 'heated', 'trainLine', 'trainLineLength', 'consultant_id', 'pricePerFloor', 'electricity', 'haveCranes', 'unknownMovingDate', 'antiDustOnly', 'passive_why', 'rangeMinPricePerFloor', 'rangeMaxPricePerFloor', 'rangeMinArea', 'rangeMaxArea', 'rangeMinCeilingHeight', 'rangeMaxCeilingHeight', 'maxDistanceFromMKAD', 'water', 'sewerage', 'gaz', 'steam', 'shelving', 'maxElectricity'], 'integer'],
			[['status', 'regions', 'directions', 'districts', 'description', 'created_at', 'updated_at', 'movingDate', 'passive_why_comment', 'all', 'dateStart', 'dateEnd', 'objectTypes', 'objectTypesGeneral', 'objectClasses', 'gateTypes'], 'safe'],
			[['consultant_ids'], 'each', 'rule' => ['integer']],
			[['with_passive_consultant'], 'boolean'],
		];
	}

	/**
	 * @throws ValidateException
	 * @throws ErrorException
	 */
	public function search(array $params): ActiveDataProvider
	{
		$query = Request::find()->distinct()
		                ->joinWith(['objectTypesGeneral', 'objectTypes', 'objectClasses', 'gateTypes', 'directions', 'districts', 'regions'])
		                ->with([
			                'consultant.userProfile',
			                'directions', 'districts', 'regions.info',
			                'contact.emails', 'contact.phones',
			                'company.requests', 'company.contacts', 'company.objects',
			                'mainTimeline.doneTimelineSteps'
		                ]);

		$dataProvider = new ActiveDataProvider([
			'query'      => $query,
			'pagination' => [
				'defaultPageSize' => 25,
				'pageSizeLimit'   => [0, 50],
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
							Request::field('expressRequest') => SORT_ASC,
							Request::field('updated_at')     => SORT_ASC
						],
						'desc' => [
							new Expression('FIELD(request.status, 2,0,1) DESC'),
							Request::field('expressRequest') => SORT_DESC,
							Request::field('updated_at')     => SORT_DESC
						],
					]
				]
			]
		]);

		$this->load($params);
		$this->validateOrThrow();

		if ($this->isFilterTrue($this->with_passive_consultant)) {
			$query->innerJoinWith(['consultant' => function (UserQuery $query) {
				$query->andWhere(['!=', User::field('status'), User::STATUS_ACTIVE]);
			}]);
		}

		if (!empty($this->all)) {
			$query->joinWith(['company.contacts']);

			$query->andFilterWhere([
				'or',
				[Request::field('id') => $this->all],
				['like', Company::field('nameEng'), $this->all],
				['like', Company::field('nameRu'), $this->all],
				['like', Company::field('individual_full_name'), $this->all],
				[
					'like',
					SQLHelper::concatWithCoalesce([
						Contact::field('first_name'),
						Contact::field('middle_name'),
						Contact::field('last_name')
					]),
					$this->all
				]
			]);
		}

		$query->andFilterWhere([Request::field('consultant_id') => $this->consultant_ids]);

		$query->andFilterWhere([
			Request::field('id')                            => $this->id,
			Request::field('company_id')                    => $this->company_id,
			Request::field('dealType')                      => $this->dealType,
			Request::field('expressRequest')                => $this->expressRequest,
			Request::field('distanceFromMKAD')              => $this->distanceFromMKAD,
			Request::field('distanceFromMKADnotApplicable') => $this->distanceFromMKADnotApplicable,
			Request::field('minCeilingHeight')              => $this->minCeilingHeight,
			Request::field('maxCeilingHeight')              => $this->maxCeilingHeight,
			Request::field('firstFloorOnly')                => $this->firstFloorOnly,
			Request::field('outside_mkad')                  => $this->outside_mkad,
			Request::field('minArea')                       => $this->minArea,
			Request::field('maxArea')                       => $this->maxArea,
			Request::field('trainLine')                     => $this->trainLine,
			Request::field('trainLineLength')               => $this->trainLineLength,
			Request::field('consultant_id')                 => $this->consultant_id,
			Request::field('pricePerFloor')                 => $this->pricePerFloor,
			Request::field('electricity')                   => $this->electricity,
			Request::field('haveCranes')                    => $this->haveCranes,
			Request::field('status')                        => $this->status,
			Request::field('unknownMovingDate')             => $this->unknownMovingDate,
			Request::field('antiDustOnly')                  => $this->antiDustOnly,
			Request::field('passive_why')                   => $this->passive_why,
			Request::field('water')                         => $this->water,
			Request::field('steam')                         => $this->steam,
			Request::field('sewerage')                      => $this->sewerage,
			Request::field('gaz')                           => $this->gaz,
			Request::field('shelving')                      => $this->shelving,

			RequestObjectType::field('object_type')   => $this->objectTypes,
			RequestObjectTypeGeneral::field('type')   => $this->objectTypesGeneral,
			RequestObjectClass::field('object_class') => $this->objectClasses,
			RequestGateType::field('gate_type')       => $this->gateTypes,
			RequestDirection::field('direction')      => $this->directions,
			RequestDistrict::field('district')        => $this->districts,
		]);


		if ($this->heated !== null) {
			$query->andFilterWhere([
				'or',
				[Request::field('heated') => $this->heated],
				['is', Request::field('heated'), new Expression('null')]
			]);
		}

		if ($this->isFilterTrue($this->region_neardy)) {
			// TODO: узнать каике регионы являются ближайшими к МО
			$query->andFilterWhere(['!=', 'request_region.region', 0]);
		} else {
			$query->andFilterWhere([RequestRegion::field('region') => $this->regions]);
		}

		$query->andFilterWhere(['>', Request::field('created_at'), $this->dateStart]);
		$query->andFilterWhere(['<', Request::field('created_at'), $this->dateEnd]);

		$query->andFilterWhere(['like', Request::field('description'), $this->description])
		      ->andFilterWhere(['like', Request::field('passive_why_comment'), $this->passive_why_comment])
		      ->andFilterWhere(['<=', Request::field('maxArea'), $this->rangeMaxArea])
		      ->andFilterWhere(['>=', Request::field('maxArea'), $this->rangeMinArea])
		      ->andFilterWhere(['<=', Request::field('pricePerFloor'), $this->rangeMaxPricePerFloor])
		      ->andFilterWhere(['>=', Request::field('pricePerFloor'), $this->rangeMinPricePerFloor])
		      ->andFilterWhere(['<=', Request::field('maxCeilingHeight'), $this->rangeMaxCeilingHeight])
		      ->andFilterWhere(['>=', Request::field('maxCeilingHeight'), $this->rangeMinCeilingHeight])
		      ->andFilterWhere(['<=', Request::field('distanceFromMKAD'), $this->maxDistanceFromMKAD])
		      ->andFilterWhere(['<=', Request::field('electricity'), $this->maxElectricity]);

		return $dataProvider;
	}
}
