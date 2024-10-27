<?php

namespace app\models;

use app\helpers\DateTimeHelper;
use app\kernel\common\models\exceptions\ValidateException;
use app\kernel\common\models\Form\Form;
use app\models\miniModels\Phone;
use Exception;
use yii\base\ErrorException;
use yii\data\ActiveDataProvider;
use yii\db\Expression;

/**
 * CompanySearch represents the model behind the search form of `app\models\Company`.
 */
class CompanySearch extends Form
{
	public $id;
	public $nameEng;
	public $nameRu;
	public $noName;
	public $formOfOrganization;
	public $companyGroup_id;
	public $status;
	public $consultant_id;
	public $activityGroup;
	public $activityProfile;
	public $rating;
	public $categories;
	public $dateStart;
	public $dateEnd;
	public $broker_id;
	public $active;
	public $passive_why;

	public $all;
	public $processed;

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array
	{
		return [
			[['noName', 'companyGroup_id', 'status', 'consultant_id', 'broker_id', 'activityGroup', 'activityProfile', 'active', 'formOfOrganization', 'processed', 'passive_why', 'rating'], 'integer'],
			[['id', 'all', 'nameEng', 'nameRu', 'categories', 'dateStart', 'dateEnd'], 'safe'],
		];
	}

	/**
	 * @throws Exception
	 */
	public function load($data, $formName = null): bool
	{
		if (isset($data['dateStart']) || isset($data['dateEnd'])) {
			$data['dateStart'] = $data['dateStart'] ?? DateTimeHelper::makef('01.01.1970', 'Y-m-d');
			$data['dateEnd']   = $data['dateEnd'] ?? DateTimeHelper::nowf('Y-m-d');
		}

		return parent::load($data, $formName);
	}

	/**
	 * @param $params
	 *
	 * @return ActiveDataProvider
	 * @throws ValidateException
	 * @throws ErrorException
	 * @throws Exception
	 */
	public function search($params): ActiveDataProvider
	{
		$query = Company::find()
		                ->distinct()
		                ->select([Company::field('*')])
		                ->joinWith(['requests', 'categories', 'contacts.phones'])
		                ->with([
			                'requests' => function ($query) {
				                $query->with(['timelines' => function ($query) {
					                $query->with(['timelineSteps'])->where(['timeline.status' => Timeline::STATUS_ACTIVE]);
				                }]);
			                },
			                'logo',
			                'companyGroup',
			                'consultant.userProfile',
			                'productRanges',
			                'mainContact.emails',
			                'mainContact.phones',
			                'categories',
			                'objects.offerMix.generalOffersMix',
			                'objects.objectFloors'
		                ]);

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
					'created_at',
					'nameRu',
					'rating',
					'status',
					'requests' => [
						'asc'  => new Expression('case when request.status = 1 then request.created_at else NULL end ASC'),
						'desc' => new Expression('case when request.status = 1 then request.created_at else NULL end DESC'),
					],
					'default'  => [
						'asc'     => [
							new Expression('case when NOW() BETWEEN company.created_at AND DATE_ADD(company.created_at, INTERVAL 12 HOUR) then company.created_at else NULL end ASC'),
							new Expression("FIELD(request.status, 0,2,1) ASC"),
							// 'request.related_updated_at' => SORT_ASC,
							new Expression("IF(request.related_updated_at, request.related_updated_at, request.created_at) ASC"),
							'request.created_at' => SORT_ASC,
							'request.updated_at' => SORT_ASC,
							'company.created_at' => SORT_ASC
						],
						'desc'    => [
							new Expression('case when NOW() BETWEEN company.created_at AND DATE_ADD(company.created_at, INTERVAL 12 HOUR) then company.created_at else NULL end DESC'),
							new Expression("FIELD(request.status, 0,2,1) DESC"),
							// 'request.related_updated_at' => SORT_DESC,
							new Expression("IF(request.related_updated_at, request.related_updated_at, request.created_at) DESC"),
							'request.created_at' => SORT_DESC,
							'request.updated_at' => SORT_DESC,
							'company.created_at' => SORT_DESC
						],
						'default' => SORT_DESC,
					],
				],
			]
		]);

		$this->load($params);
		$this->validateOrThrow();

		$query->orFilterWhere([Company::field('id') => $this->all])
		      ->orFilterWhere([Company::field('nameEng') => ['like', Company::field('nameEng'), $this->all]])
		      ->orFilterWhere([Company::field('nameRu') => ['like', Company::field('nameRu'), $this->all]])
		      ->orFilterWhere([Company::field('nameBrand') => ['like', Company::field('nameBrand'), $this->all]])
		      ->orFilterWhere([Contact::field('first_name') => ['like', Contact::field('first_name'), $this->all]])
		      ->orFilterWhere([Contact::field('middle_name') => ['like', Contact::field('middle_name'), $this->all]])
		      ->orFilterWhere([Contact::field('last_name') => ['like', Contact::field('last_name'), $this->all]])
		      ->orFilterWhere([Phone::field('phone') => ['like', Phone::field('phone'), $this->all]]);


		if ($this->all) {
			$query->orderBy(new Expression("
                 (
                    IF (`company`.`id` = '{$this->all}', 250, 0) 
                    + IF (`company`.`id` LIKE '%{$this->all}%', 90, 0) 
                    + IF (`phone`.`phone` LIKE '%{$this->all}%', 40, 0) 
                    + IF (`company`.`nameRu` LIKE '%{$this->all}%', 80, 0) 
                    + IF (`company`.`nameRu` = '{$this->all}', 250, 0) 
                    + IF (`company`.`nameEng` LIKE '%{$this->all}%', 80, 0) 
                    + IF (`company`.`nameEng` = '{$this->all}', 250, 0) 
                    + IF (`company`.`nameBrand` LIKE '%{$this->all}%', 50, 0) 
                    + IF (`contact`.`first_name` LIKE '%{$this->all}%', 30, 0) 
                    + IF (`contact`.`middle_name` LIKE '%{$this->all}%', 30, 0) 
                    + IF (`contact`.`last_name` LIKE '%{$this->all}%', 30, 0) 
                )
                DESC
            "));
		}
		$query->andFilterWhere([
			Company::field('id')              => $this->id,
			Company::field('noName')          => $this->noName,
			Company::field('companyGroup_id') => $this->companyGroup_id,
			Company::field('status')          => $this->status,
			Company::field('consultant_id')   => $this->consultant_id,
			Company::field('broker_id')       => $this->broker_id,
			Company::field('activityGroup')   => $this->activityGroup,
			Company::field('activityProfile') => $this->activityProfile,
			Category::field('category')       => $this->categories
		]);

		if (ArrayHelper::isArray($this->categories) && ArrayHelper::length($this->categories) > 1) {
			$query->groupBy(Company::field('id'));
			$query->andFilterHaving(['>', new Expression('COUNT(DISTINCT category.category)'), count($this->categories) - 1]);
		}


		$query->andFilterWhere(['like', Company::field('nameEng'), $this->nameEng])
		      ->andFilterWhere(['like', Company::field('nameRu'), $this->nameRu])
		      ->andFilterWhere(['like', Company::field('formOfOrganization'), $this->formOfOrganization])
		      ->andFilterWhere(['between', Company::field('created_at'), $this->dateStart, $this->dateEnd]);


		return $dataProvider;
	}
}
