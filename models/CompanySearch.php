<?php

namespace app\models;

use app\components\ExpressionBuilder\FieldExpressionBuilder;
use app\components\ExpressionBuilder\IfExpressionBuilder;
use app\kernel\common\models\exceptions\ValidateException;
use app\kernel\common\models\Form\Form;
use app\models\ActiveQuery\TimelineQuery;
use app\models\ActiveQuery\UserQuery;
use app\models\miniModels\Phone;
use app\models\views\CompanySearchView;
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
	public $is_individual;

	public $all;
	public $processed;
	public $product_ranges;
	public $activity_group_ids   = [];
	public $activity_profile_ids = [];

	public $without_product_ranges  = false;
	public $with_passive_consultant = false;
	public $show_product_ranges;
	public $requests_filter;
	public $requests_area_min;
	public $requests_area_max;
	public $folder_ids;

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array
	{
		return [
			[['noName', 'companyGroup_id', 'status', 'consultant_id', 'broker_id', 'activityGroup', 'activityProfile', 'active', 'formOfOrganization', 'processed', 'passive_why', 'rating'], 'integer'],
			[['id', 'all', 'nameEng', 'nameRu', 'categories', 'dateStart', 'dateEnd', 'product_ranges'], 'safe'],
			[['activity_group_ids', 'activity_profile_ids', 'folder_ids'], 'each', 'rule' => ['integer']],
			[['without_product_ranges', 'with_passive_consultant', 'show_product_ranges'], 'boolean'],
			['requests_filter', 'string'],
			['requests_filter', 'in', 'range' => ['none', 'active', 'not-active', 'passive']],
			[['requests_area_min', 'requests_area_max'], 'integer'],
		];
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
		$query = CompanySearchView::find()
		                          ->select([
			                          Company::field('*'),
			                          'last_call_rel_id'      => 'last_call_rel.id',
			                          'objects_count'         => 'COUNT(DISTINCT ' . Objects::field('id') . ' )',
			                          'requests_count'        => 'COUNT(DISTINCT request.id)',
			                          'active_requests_count' => 'COUNT(DISTINCT CASE WHEN request.status = 1 THEN request.id ELSE NULL END)',
			                          'contacts_count'        => 'COUNT(DISTINCT contact.id)',
			                          'active_contacts_count' => 'COUNT(DISTINCT CASE WHEN contact.status = 1 THEN contact.id ELSE NULL END)',
		                          ])
		                          ->joinWith(['requests', 'categories', 'contacts.phones', 'objects', 'productRanges', 'companyActivityGroups', 'companyActivityProfiles'])
		                          ->joinWith(['chatMember cm'])
		                          ->leftJoinLastCallRelation()
		                          ->with([
			                          'requests' => function ($query) {
				                          $query->with(['timelines' => function (TimelineQuery $query) {
					                          $query->with(['timelineSteps', 'consultant'])->active();
				                          }]);
			                          },
			                          'logo',
			                          'companyGroup',
			                          'consultant.userProfile',
			                          'mainContact.emails', 'mainContact.phones',
			                          'generalContact.phones', 'generalContact.emails', 'generalContact.websites',
			                          'categories',
			                          'objects.offerMix.generalOffersMix',
			                          'objects.objectFloors',
			                          'lastCall'
		                          ])->groupBy(Company::field('id'));

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
						'asc'  => IfExpressionBuilder::create()
						                             ->condition('request.status = 1')
						                             ->left(Request::field('created_at'))
						                             ->right('NULL')
						                             ->beforeBuild(fn($expression) => "$expression ASC")
						                             ->build(),
						'desc' => IfExpressionBuilder::create()
						                             ->condition('request.status = 1')
						                             ->left(Request::field('created_at'))
						                             ->right('NULL')
						                             ->beforeBuild(fn($expression) => "$expression DESC")
						                             ->build()
					],
					'default'  => [
						'asc'     => [
							IfExpressionBuilder::create()
							                   ->condition('NOW() BETWEEN company.created_at AND DATE_ADD(company.created_at, INTERVAL 12 HOUR)')
							                   ->left(Company::field('created_at'))
							                   ->right('NULL')
							                   ->beforeBuild(fn($expression) => "$expression ASC")
							                   ->build(),
							FieldExpressionBuilder::create()
							                      ->field(Request::field('status'))
							                      ->values(0, 2, 1)
							                      ->beforeBuild(fn($expression) => "$expression ASC")
							                      ->build(),
							"COALESCE(last_call_rel.created_at, company.updated_at, company.created_at)" => SORT_ASC,
							IfExpressionBuilder::create()
							                   ->condition(Request::field('related_updated_at'))
							                   ->left(Request::field('related_updated_at'))
							                   ->right(Request::field('created_at'))
							                   ->beforeBuild(fn($expression) => "$expression ASC")
							                   ->build(),
							Request::field('created_at')                                                 => SORT_ASC,
							Request::field('updated_at')                                                 => SORT_ASC,
							Company::field('created_at')                                                 => SORT_ASC
						],
						'desc'    => [
							IfExpressionBuilder::create()
							                   ->condition('NOW() BETWEEN company.created_at AND DATE_ADD(company.created_at, INTERVAL 12 HOUR)')
							                   ->left(Company::field('created_at'))
							                   ->right('NULL')
							                   ->beforeBuild(fn($expression) => "$expression DESC")
							                   ->build(),
							FieldExpressionBuilder::create()
							                      ->field(Request::field('status'))
							                      ->values(0, 2, 1)
							                      ->beforeBuild(fn($expression) => "$expression DESC")
							                      ->build(),
							"COALESCE(last_call_rel.created_at, company.updated_at, company.created_at)" => SORT_DESC,
							IfExpressionBuilder::create()
							                   ->condition(Request::field('related_updated_at'))
							                   ->left(Request::field('related_updated_at'))
							                   ->right(Request::field('created_at'))
							                   ->beforeBuild(fn($expression) => "$expression DESC")
							                   ->build(),
							Request::field('created_at')                                                 => SORT_DESC,
							Request::field('updated_at')                                                 => SORT_DESC,
							Company::field('created_at')                                                 => SORT_DESC
						],
						'default' => SORT_DESC
					],
				],
			]
		]);

		$this->load($params);
		$this->validateOrThrow();

		$query->orFilterWhere([Company::field('id') => $this->all])
		      ->orFilterWhere(['like', Company::field('nameEng'), $this->all])
		      ->orFilterWhere(['like', Company::field('nameRu'), $this->all])
		      ->orFilterWhere(['like', Company::field('nameBrand'), $this->all])
		      ->orFilterWhere(['like', Contact::field('first_name'), $this->all])
		      ->orFilterWhere(['like', Contact::field('middle_name'), $this->all])
		      ->orFilterWhere(['like', Contact::field('last_name'), $this->all])
		      ->orFilterWhere(['like', Phone::field('phone'), $this->all])
		      ->orFilterWhere(['like', Company::field('individual_full_name'), $this->all]);


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

		if ($this->isFilterTrue($this->without_product_ranges)) {
			$query->andWhere([Productrange::field('id') => null]);
		}

		if ($this->isFilterTrue($this->with_passive_consultant)) {
			$query->innerJoinWith(['consultant' => function (UserQuery $query) {
				$query->andWhere(['!=', User::field('status'), User::STATUS_ACTIVE]);
			}]);
		}

		if (!is_null($this->requests_filter)) {
			switch ($this->requests_filter) {
				case 'active':
				{
					$query->andHaving(['!=', 'active_requests_count', 0]);
					break;
				}
				case 'not-active':
				{
					$query->andHaving(['active_requests_count' => 0]);
					break;
				}
				case 'passive':
				{
					$query->andHaving('requests_count > active_requests_count');
					break;
				}
				case 'none':
				{
					$query->andHaving(['requests_count' => 0]);
					break;
				}
			}
		}

		if ($this->hasFilter($this->folder_ids)) {
			$query->innerJoinWith(['folderEntities'], false)->andWhere([FolderEntity::field('folder_id') => $this->folder_ids]);
		}

		$query->andFilterWhere([
			Company::field('id')                                 => $this->id,
			Company::field('noName')                             => $this->noName,
			Company::field('companyGroup_id')                    => $this->companyGroup_id,
			Company::field('status')                             => $this->status,
			Company::field('consultant_id')                      => $this->consultant_id,
			Company::field('broker_id')                          => $this->broker_id,
			Company::field('show_product_ranges')                => $this->show_product_ranges,
			Company::field('activityGroup')                      => $this->activityGroup,
			Company::field('activityProfile')                    => $this->activityProfile,
			CompanyActivityGroup::field('activity_group_id')     => $this->activity_group_ids,
			CompanyActivityProfile::field('activity_profile_id') => $this->activity_profile_ids,
			Category::field('category')                          => $this->categories,
			Company::field('is_individual')                      => $this->is_individual,
			Productrange::field('product')                       => $this->product_ranges
		]);

		$query->andFilterWhere(['like', Company::field('nameEng'), $this->nameEng])
		      ->andFilterWhere(['like', Company::field('nameRu'), $this->nameRu])
		      ->andFilterWhere(['like', Company::field('formOfOrganization'), $this->formOfOrganization])
		      ->andFilterWhere(['>=', Company::field('created_at'), $this->dateStart])
		      ->andFilterWhere(['<=', Company::field('created_at'), $this->dateEnd]);

		$query->andFilterWhere([
			'and',
			['<=', 'LEAST(request.maxArea, request.minArea)', $this->requests_area_max],
			['>=', 'GREATEST(request.maxArea, request.minArea)', $this->requests_area_min],
		]);


		return $dataProvider;
	}
}
