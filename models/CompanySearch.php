<?php

namespace app\models;

use app\components\ExpressionBuilder\FieldExpressionBuilder;
use app\components\ExpressionBuilder\IfExpressionBuilder;
use app\helpers\ArrayHelper;
use app\kernel\common\models\exceptions\ValidateException;
use app\kernel\common\models\Form\Form;
use app\models\ActiveQuery\TimelineQuery;
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
			                          'objects_count'  => 'COUNT(DISTINCT ' . Objects::field('id') . ' )',
			                          'requests_count' => 'COUNT(DISTINCT request.id)',
			                          'contacts_count' => 'COUNT(DISTINCT contact.id)'
		                          ])
		                          ->joinWith(['requests', 'categories', 'contacts.phones', 'objects'])
		                          ->with([
			                          'requests' => function ($query) {
				                          $query->with(['timelines' => function (TimelineQuery $query) {
					                          $query->with(['timelineSteps'])->active();
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
							IfExpressionBuilder::create()
							                   ->condition(Request::field('related_updated_at'))
							                   ->left(Request::field('related_updated_at'))
							                   ->right(Request::field('created_at'))
							                   ->beforeBuild(fn($expression) => "$expression ASC")
							                   ->build(),
							Request::field('created_at') => SORT_ASC,
							Request::field('updated_at') => SORT_ASC,
							Company::field('created_at') => SORT_ASC
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
							IfExpressionBuilder::create()
							                   ->condition(Request::field('related_updated_at'))
							                   ->left(Request::field('related_updated_at'))
							                   ->right(Request::field('created_at'))
							                   ->beforeBuild(fn($expression) => "$expression DESC")
							                   ->build(),
							Request::field('created_at') => SORT_DESC,
							Request::field('updated_at') => SORT_DESC,
							Company::field('created_at') => SORT_DESC
						],
						'default' => SORT_DESC
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
		      ->andFilterWhere(['>=', Company::field('created_at'), $this->dateStart])
		      ->andFilterWhere(['<=', Company::field('created_at'), $this->dateEnd]);


		return $dataProvider;
	}
}
