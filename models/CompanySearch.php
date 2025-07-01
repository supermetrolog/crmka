<?php

namespace app\models;

use app\components\ExpressionBuilder\IfExpressionBuilder;
use app\helpers\ArrayHelper;
use app\kernel\common\models\AQ\AQ;
use app\kernel\common\models\exceptions\ValidateException;
use app\kernel\common\models\Form\Form;
use app\models\ActiveQuery\ChatMemberMessageQuery;
use app\models\ActiveQuery\CommercialOfferQuery;
use app\models\ActiveQuery\CompanyQuery;
use app\models\ActiveQuery\RequestQuery;
use app\models\ActiveQuery\SurveyQuery;
use app\models\ActiveQuery\TaskQuery;
use app\models\miniModels\Phone;
use app\models\search\expressions\CompanySearchExpressions;
use app\models\views\CompanySearchView;
use Exception;
use yii\base\ErrorException;
use yii\data\ActiveDataProvider;
use yii\data\Sort;
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
	public $with_current_user_tasks = false;
	public $show_product_ranges;
	public $requests_filter;
	public $requests_area_min;
	public $requests_area_max;
	public $folder_ids;
	public $current_user_id;

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array
	{
		return [
			[['noName', 'companyGroup_id', 'status', 'consultant_id', 'broker_id', 'activityGroup', 'activityProfile', 'active', 'formOfOrganization', 'processed', 'passive_why', 'rating'], 'integer'],
			[['id', 'all', 'nameEng', 'nameRu', 'categories', 'dateStart', 'dateEnd', 'product_ranges'], 'safe'],
			[['activity_group_ids', 'activity_profile_ids', 'folder_ids'], 'each', 'rule' => ['integer']],
			[['without_product_ranges', 'with_passive_consultant', 'show_product_ranges', 'with_current_user_tasks'], 'boolean'],
			['requests_filter', 'string'],
			['requests_filter', 'in', 'range' => ['none', 'active', 'not-active', 'passive']],
			[['requests_area_min', 'requests_area_max', 'current_user_id'], 'integer'],
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
			                          'active_contacts_count' => 'COUNT(DISTINCT CASE WHEN contact.status = 1 THEN contact.id ELSE NULL END)'
		                          ])
		                          ->joinWith(['requests', 'categories', 'contacts.phones', 'objects', 'productRanges', 'companyActivityGroups', 'companyActivityProfiles'])
		                          ->joinWith(['chatMember cm'])
		                          ->leftJoinLastCallRelation()
		                          ->with([
			                          'requests',
			                          'logo',
			                          'companyGroup',
			                          'consultant.userProfile',
			                          'mainContact.emails', 'mainContact.phones',
			                          'generalContact.phones', 'generalContact.emails', 'generalContact.websites',
			                          'categories',
			                          'objects.offerMix.generalOffersMix',
			                          'objects.objectFloors',
			                          'lastCall',
			                          'chatMember',
			                          'lastSurvey.user.userProfile', 'lastSurvey.calls',
			                          'lastSurvey.contact.emails', 'lastSurvey.contact.websites', 'lastSurvey.contact.phones',
			                          'lastSurvey.contact.consultant.userProfile', 'lastSurvey.contact.wayOfInformings',
			                          'lastSurvey.tasks.tags', 'lastSurvey.tasks.createdByUser.userProfile', 'lastSurvey.tasks.user.userProfile',
			                          'lastSurvey.tasks.observers.user.userProfile', 'lastSurvey.tasks.targetUserObserver',
			                          'lastSurvey.chatMemberMessage.fromChatMember.user.userProfile',
			                          'lastSurvey.chatMemberMessage.files',
			                          'pinnedMessages.chatMemberMessage.fromChatMember.user.userProfile', 'pinnedMessages.chatMemberMessage.files',
		                          ])->groupBy(Company::field('id'));

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

		if ($this->hasFilter($this->current_user_id)) {
			$query->addSelect([
				'tasks_count'           => 'COUNT(DISTINCT task.id)',
				'has_pending_survey'    => '(sd.id is not null)',
				'pending_survey_status' => 'sd.status'
			]);

			$query->joinWith(['tasks' => function (TaskQuery $subquery) {
				$subquery->andOnCondition([
					'and',
					[Task::field('user_id') => $this->current_user_id],
					['!=', Task::field('status'), Task::STATUS_DONE]
				]);
			}], false, $this->isFilterTrue($this->with_current_user_tasks) ? 'INNER JOIN' : 'LEFT JOIN')
			      ->leftJoin(
				      ['sd' => Survey::getTable()],
				      ['and', ['sd.status' => [Survey::STATUS_DRAFT, Survey::STATUS_DELAYED], 'sd.deleted_at' => null], 'sd.chat_member_id = cm.id', 'sd.user_id = :user_id'],
				      ['user_id' => $this->current_user_id]
			      );
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


		return new ActiveDataProvider([
			'query'      => $query,
			'pagination' => [
				'pageSize' => 50
			],
			'sort'       => $this->createSort($query)
		]);
	}

	/**
	 * @throws ErrorException
	 */
	private function createSort($query): Sort
	{
		$sort = new Sort([
			'enableMultiSort' => true,
			'defaultOrder'    => [
				'default' => SORT_DESC
			],
			'attributes'      => $this->getSortAttributes()
		]);

		$this->applySortJoins($query, $sort);

		return $sort;
	}

	/**
	 * @throws ErrorException
	 */
	private function getSortAttributes(): array
	{
		return [
			'created_at',
			'updated_at'              => [
				'asc'  => [
					'COALESCE(company.updated_at, company.created_at)' => SORT_ASC
				],
				'desc' => [
					'COALESCE(company.updated_at, company.created_at)' => SORT_DESC
				]
			],
			'nameRu',
			'rating',
			'status',
			'last_survey_created_at'  => [
				'asc'  => [
					new Expression('ls.last_survey_completed_at IS NOT NULL DESC'),
					"COALESCE(ls.last_survey_completed_at, ls.last_survey_created_at)" => SORT_ASC,
					'COALESCE(company.updated_at, company.created_at)'                 => SORT_ASC
				],
				'desc' => [
					"COALESCE(ls.last_survey_completed_at, ls.last_survey_created_at)" => SORT_DESC,
					'COALESCE(company.updated_at, company.created_at)'                 => SORT_DESC
				]
			],
			'last_message_created_at' => [
				'asc'  => [
					new Expression('lcmm.last_message_created_at IS NOT NULL DESC'),
					'lcmm.last_message_created_at'                     => SORT_ASC,
					'COALESCE(company.updated_at, company.created_at)' => SORT_ASC
				],
				'desc' => [
					'lcmm.last_message_created_at'                     => SORT_DESC,
					'COALESCE(company.updated_at, company.created_at)' => SORT_DESC
				]
			],
			'last_task_created_at'    => [
				'asc'  => [
					new Expression('lt.last_task_created_at IS NOT NULL DESC'),
					'lt.last_task_created_at'                          => SORT_ASC,
					'COALESCE(company.updated_at, company.created_at)' => SORT_ASC
				],
				'desc' => [
					'lt.last_task_created_at'                          => SORT_DESC,
					'COALESCE(company.updated_at, company.created_at)' => SORT_DESC
				]
			],
			'last_request_created_at' => [
				'asc'  => [
					new Expression('lr.last_request_created_at IS NOT NULL DESC'),
					'lr.last_request_created_at'                       => SORT_ASC,
					'COALESCE(company.updated_at, company.created_at)' => SORT_ASC
				],
				'desc' => [
					'lr.last_request_created_at'                       => SORT_DESC,
					'COALESCE(company.updated_at, company.created_at)' => SORT_DESC
				]
			],
			'last_object_created_at'  => [
				'asc'  => [
					new Expression('lob.last_object_created_at IS NOT NULL DESC'),
					'lob.last_object_created_at'                       => SORT_ASC,
					'COALESCE(company.updated_at, company.created_at)' => SORT_ASC
				],
				'desc' => [
					'lob.last_object_created_at'                       => SORT_DESC,
					'COALESCE(company.updated_at, company.created_at)' => SORT_DESC
				]
			],
			'last_offer_updated_at'   => [
				'asc'  => [
					new Expression('lof.id IS NOT NULL DESC'),
					"COALESCE(lof.last_offer_updated_at, lof.last_offer_created_at)" => SORT_ASC,
					'COALESCE(company.updated_at, company.created_at)'               => SORT_ASC
				],
				'desc' => [
					"COALESCE(lof.last_offer_updated_at, lof.last_offer_created_at)" => SORT_DESC,
					'COALESCE(company.updated_at, company.created_at)'               => SORT_DESC
				]
			],
			'requests'                => [
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
			'activity'                => [
				'asc' => [
					new Expression('CASE
			            WHEN MIN(task.start) < DATE_ADD(CURDATE(), INTERVAL 5 DAY) THEN 1
			            ELSE 2
			        END ASC'),
					'MIN(task.start)'            => SORT_ASC,
					CompanySearchExpressions::surveyDelayedOrder(),
					CompanySearchExpressions::surveyCompletedOrder(),
					CompanySearchExpressions::recentlyCreatedOrder(),
					CompanySearchExpressions::byRequestStatusOrder(),
					CompanySearchExpressions::requestRelatedOrder(),
					Request::field('created_at') => SORT_ASC,
					Request::field('updated_at') => SORT_ASC,
					Company::field('created_at') => SORT_ASC
				]
			],
			'default'                 => [
				'asc'     => [
					CompanySearchExpressions::recentlyCreatedOrder(),
					CompanySearchExpressions::byRequestStatusOrder(),
					"COALESCE(last_call_rel.created_at, company.updated_at, company.created_at)" => SORT_ASC,
					CompanySearchExpressions::requestRelatedOrder(),
					Request::field('created_at')                                                 => SORT_ASC,
					Request::field('updated_at')                                                 => SORT_ASC,
					Company::field('created_at')                                                 => SORT_ASC
				],
				'desc'    => [
					CompanySearchExpressions::recentlyCreatedOrder('DESC'),
					CompanySearchExpressions::byRequestStatusOrder('DESC'),
					"COALESCE(last_call_rel.created_at, company.updated_at, company.created_at)" => SORT_DESC,
					CompanySearchExpressions::requestRelatedOrder('DESC'),
					Request::field('created_at')                                                 => SORT_DESC,
					Request::field('updated_at')                                                 => SORT_DESC,
					Company::field('created_at')                                                 => SORT_DESC
				],
				'default' => SORT_DESC
			]
		];
	}

	private function getJoinMap(): array
	{
		return [
			'last_survey_created_at'  => fn(CompanyQuery $query) => $query->leftJoin(['ls' => $this->makeLastSurveyQuery()], 'ls.chat_member_id = cm.id'),
			'last_message_created_at' => fn(CompanyQuery $query) => $query->leftJoin(['lcmm' => $this->makeLastChatMemberMessageQuery()], 'lcmm.to_chat_member_id = cm.id'),
			'last_task_created_at'    => fn(CompanyQuery $query) => $query->leftJoin(['lt' => $this->makeLastTaskQuery()], 'lt.company_id = company.id'),
			'last_request_created_at' => fn(CompanyQuery $query) => $query->leftJoin(['lr' => $this->makeLastRequestQuery()], 'lr.company_id = company.id'),
			'last_object_created_at'  => fn(CompanyQuery $query) => $query->leftJoin(['lob' => $this->makeLastObjectQuery()], 'lob.company_id = company.id'),
			'last_offer_updated_at'   => fn(CompanyQuery $query) => $query->leftJoin(['lof' => $this->makeLastOfferQuery()], 'lof.company_id = company.id'),
			'activity'                => fn(CompanyQuery $query) => $query->leftJoin(['lps' => $this->makeLastProcessedSurveyQuery()], 'lps.chat_member_id = cm.id'),
		];
	}

	private function applySortJoins(CompanyQuery $query, Sort $sort): void
	{
		$fieldsToSort = ArrayHelper::keys($sort->getAttributeOrders());
		$joinMap      = $this->getJoinMap();

		foreach ($fieldsToSort as $field) {
			if (isset($joinMap[$field])) {
				$joinMap[$field]($query);
			}
		}
	}

	/**
	 * @throws ErrorException
	 */
	private function makeLastChatMemberMessageQuery(): ChatMemberMessageQuery
	{
		return ChatMemberMessage::find()
		                        ->from(ChatMemberMessage::getTable())
		                        ->select(['id', 'to_chat_member_id', 'last_message_created_at' => 'MAX(created_at)', 'deleted_at'])
		                        ->groupBy(['to_chat_member_id'])
		                        ->notDeleted();
	}

	/**
	 * @throws ErrorException
	 */
	private function makeLastTaskQuery(): TaskQuery
	{
		return Task::find()
		           ->from(Task::getTable())
		           ->select([
			           'tre.entity_id AS company_id',
			           'last_task_created_at' => 'MAX(task.created_at)',
		           ])
		           ->innerJoin(['tre' => TaskRelationEntity::getTable()], 'tre.task_id = task.id')
		           ->where([
			           'tre.entity_type' => Company::getMorphClass(),
			           'task.deleted_at' => null,
			           'tre.deleted_at'  => null,
		           ])
		           ->groupBy('tre.entity_id');
	}

	/**
	 * @throws ErrorException
	 */
	private function makeLastRequestQuery(): RequestQuery
	{
		return Request::find()
		              ->from(Request::getTable())
		              ->select([
			              'company_id',
			              'last_request_created_at' => 'MAX(request.created_at)',
		              ])
		              ->groupBy('request.company_id');
	}

	/**
	 * @throws ErrorException
	 */
	private function makeLastObjectQuery(): AQ
	{
		return Objects::find()
		              ->from(Objects::getTable())
		              ->select([
			              'company_id',
			              'last_object_created_at' => 'MAX(publ_time)',
		              ])
		              ->groupBy('company_id');
	}

	/**
	 * @throws ErrorException
	 */
	private function makeLastOfferQuery(): CommercialOfferQuery
	{
		return CommercialOffer::find()
		                      ->from(CommercialOffer::getTable())
		                      ->select([
			                      'company_id',
			                      'id',
			                      'last_offer_updated_at' => 'MAX(last_update)',
			                      'last_offer_created_at' => 'MAX(publ_time)',
		                      ])
		                      ->groupBy('company_id');
	}

	/**
	 * @throws ErrorException
	 */
	private function makeLastSurveyQuery(): SurveyQuery
	{
		return Survey::find()
		             ->from(Survey::getTable())
		             ->select([
			             'id',
			             'chat_member_id',
			             'last_survey_created_at'   => 'MAX(created_at)',
			             'last_survey_completed_at' => 'MAX(completed_at)',
		             ])
		             ->andWhere(['!=', Survey::field('status'), [Survey::STATUS_DRAFT, Survey::STATUS_DELAYED]])
		             ->groupBy('chat_member_id');
	}

	private function makeLastProcessedSurveyQuery(int $delayedInterval = 5, int $completedInterval = 90): SurveyQuery
	{
		$subQuery = Survey::find()
		                  ->select(['chat_member_id', 'max_id' => 'MAX(id)'])
		                  ->where([
			                  'or',
			                  ['and',
			                   ['status' => [Survey::STATUS_COMPLETED, Survey::STATUS_CANCELED]],
			                   new Expression('NOW() > DATE_ADD(completed_at, INTERVAL :completedInterval DAY)')
			                  ],
			                  ['and',
			                   ['status' => Survey::STATUS_DELAYED],
			                   new Expression('NOW() > DATE_ADD(updated_at, INTERVAL :delayedInterval DAY)')
			                  ]
		                  ], [
			                  ':completedInterval' => $completedInterval,
			                  ':delayedInterval'   => $delayedInterval
		                  ])
		                  ->groupBy('chat_member_id');

		return Survey::find()
		             ->from(['s' => Survey::tableName()])
		             ->innerJoin(['sq' => $subQuery], 'id = sq.max_id')
		             ->select(['s.id', 's.chat_member_id', 's.status', 's.completed_at', 's.updated_at', 's.created_at']);
	}
}
