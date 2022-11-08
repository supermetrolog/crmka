<?php

namespace app\models;

use app\exceptions\ValidationErrorHttpException;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Company;
use yii\data\Sort;
use yii\db\Expression;

/**
 * CompanySearch represents the model behind the search form of `app\models\Company`.
 */
class CompanySearch extends Company
{
    public $all;
    public $categories;
    public $dateStart;
    public $dateEnd;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['noName', 'companyGroup_id', 'status', 'consultant_id', 'broker_id', 'activityGroup', 'activityProfile', 'active', 'formOfOrganization', 'processed', 'passive_why', 'rating'], 'integer'],
            [['id', 'all', 'nameEng', 'nameRu', 'officeAdress', 'legalAddress', 'ogrn', 'inn', 'kpp', 'checkingAccount', 'correspondentAccount', 'inTheBank', 'bik', 'okved', 'okpo', 'signatoryName', 'signatoryMiddleName', 'signatoryLastName', 'basis', 'documentNumber', 'description', 'created_at', 'updated_at', 'passive_why_comment', 'categories', 'dateStart', 'dateEnd'], 'safe'],
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
        $this->categories = $this->stringToArray($this->categories);
        $this->id = $this->stringToArray($this->id);
        if ($this->dateStart === null && $this->dateEnd === null) {
            return;
        }

        $this->dateStart = $this->dateStart ?? date('Y-m-d', strtotime('01.01.1970'));
        $this->dateEnd = $this->dateEnd ?? date('Y-m-d');
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
        // SELECT * FROM (SELECT DISTINCT company.id, company.nameRu, category.category FROM `company` LEFT JOIN `request` ON `company`.`id` = `request`.`company_id` LEFT JOIN `category` ON `company`.`id` = `category`.`company_id` LEFT JOIN `contact` ON `company`.`id` = `contact`.`company_id` LEFT JOIN `phone` ON `contact`.`id` = `phone`.`contact_id` WHERE category.category IN (1,0)) as fuck GROUP BY id HAVING COUNT(*) > 1
        /**
         * SELECT DISTINCT `id`, `nameEng`, `nameRu`, `noName`, `companyGroup_id`, `officeAdress`, `status`, `consultant_id`, `broker_id`, `legalAddress`, `ogrn`, `inn`, `kpp`, `checkingAccount`, `correspondentAccount`, `inTheBank`, `bik`, `okved`, `okpo`, `signatoryName`, `signatoryMiddleName`, `signatoryLastName`, `basis`, `documentNumber`, `activityGroup`, `activityProfile`, `description`, `created_at`, `updated_at`, `active`, `formOfOrganization`, `processed`, `passive_why`, `passive_why_comment`, `rating`, `latitude`, `longitude`, `nameBrand` 
         * FROM (
         *   SELECT DISTINCT `company`.*, request.status as req_status, request.related_updated_at as fuck, request.created_at as i 
         *   FROM `company` LEFT JOIN `request` ON `company`.`id` = `request`.`company_id` 
         *   ORDER BY 
         *       FIELD(request.status, 0,2,1) DESC,
         *       request.related_updated_at DESC,
         *       request.created_at DESC
         *     ) as company
         */
        $query = Company::find()->distinct()->select([
            'company.*',
            'req_status' => 'request.status',
            'req_created_at' => 'request.created_at',
            'req_updated_at' => 'request.updated_at',
            'req_related_updated_at' => "request.related_updated_at",
        ])->joinWith(['requests', 'categories', 'contacts.phones']);
        $sort = new Sort([
            'enableMultiSort' => true,
            'defaultOrder' => [
                'default' => SORT_DESC
            ],
            'attributes' => [
                'created_at',
                'nameRu',
                'rating',
                'status',
                'requests' => [
                    'asc' => new \yii\db\Expression('case when request.status = 1 then request.created_at else NULL end ASC'),
                    'desc' => new \yii\db\Expression('case when request.status = 1 then request.created_at else NULL end DESC'),
                ],
                'default' => [
                    'asc' => [
                        new \yii\db\Expression('case when NOW() BETWEEN company.created_at AND DATE_ADD(company.created_at, INTERVAL 12 HOUR) then company.created_at else NULL end ASC'),
                        new Expression("FIELD(request.status, 0,2,1) ASC"),
                        // 'request.related_updated_at' => SORT_ASC,
                        new Expression("IF(request.related_updated_at, request.related_updated_at, request.created_at) ASC"),
                        'request.created_at' => SORT_ASC,
                        'request.updated_at' => SORT_ASC,
                        'company.created_at' => SORT_ASC
                    ],
                    'desc' => [
                        new \yii\db\Expression('case when NOW() BETWEEN company.created_at AND DATE_ADD(company.created_at, INTERVAL 12 HOUR) then company.created_at else NULL end DESC'),
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
        ]);
        $query->addOrderBy($sort->getOrders());

        $wrapperQuery = Company::find()->distinct()->select((new Company())->attributes())->from($query)
            ->with([
                'requests' => function ($query) {
                    $query->with(['timelines' => function ($query) {
                        $query->with(['timelineSteps'])->where(['timeline.status' => Timeline::STATUS_ACTIVE]);
                    }]);
                },
                'companyGroup', 'broker', 'deals', 'dealsRequestEmpty', 'consultant.userProfile', 'productRanges',
                'mainContact.emails', 'mainContact.phones', 'objects.offerMix.generalOffersMix', 'objects.objectFloors',
                'categories', 'contacts' => function ($query) {
                    $query->with(['phones', 'emails', 'contactComments']);
                }
            ]);

        $dataProvider = new ActiveDataProvider([
            'query' => $wrapperQuery,
            'pagination' => [
                'defaultPageSize' => 50,
                'pageSizeLimit' => [0, 50],
            ],
        ]);

        $this->load($params, '');
        $this->normalizeProps();
        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            throw new ValidationErrorHttpException($this->getErrorSummary(false));
            return $dataProvider;
        }

        $query->orFilterWhere(['company.id' => $this->all])
            ->orFilterWhere(['like', 'contact.first_name', $this->all])
            ->orFilterWhere(['like', 'contact.middle_name', $this->all])
            ->orFilterWhere(['like', 'contact.last_name', $this->all])
            ->orFilterWhere(['like', 'phone.phone', $this->all])
            ->orFilterWhere(['like', 'company.nameEng', $this->all])
            ->orFilterWhere(['like', 'company.nameRu', $this->all])
            ->orFilterWhere(['like', 'company.nameBrand', $this->all]);
        // для релевантности
        if ($this->all) {
            $query->orderBy(new Expression("
                 (
                    IF (`company`.`id` = {$this->all}, 250, 0) 
                    + IF (`company`.`id` LIKE '%{$this->all}%', 90, 0) 
                    + IF (`phone`.`phone` LIKE '%{$this->all}%', 40, 0) 
                    + IF (`company`.`nameRu` LIKE '%{$this->all}%', 50, 0) 
                    + IF (`company`.`nameEng` LIKE '%{$this->all}%', 50, 0) 
                    + IF (`company`.`nameBrand` LIKE '%{$this->all}%', 50, 0) 
                    + IF (`contact`.`first_name` LIKE '%{$this->all}%', 30, 0) 
                    + IF (`contact`.`middle_name` LIKE '%{$this->all}%', 30, 0) 
                    + IF (`contact`.`last_name` LIKE '%{$this->all}%', 30, 0) 
                )
                DESC
            "));
        }
        $query->andFilterWhere([
            'company.id' => $this->id,
            'company.noName' => $this->noName,
            'company.companyGroup_id' => $this->companyGroup_id,
            'company.status' => $this->status,
            'company.consultant_id' => $this->consultant_id,
            'company.broker_id' => $this->broker_id,
            'company.activityGroup' => $this->activityGroup,
            'company.activityProfile' => $this->activityProfile,
            'company.created_at' => $this->created_at,
            'company.updated_at' => $this->updated_at,
            'category.category' => $this->categories
        ]);
        if ($this->categories && count($this->categories) > 1) {
            $query->groupBy('company.id');
            $query->andFilterHaving(['>', new \yii\db\Expression('COUNT(DISTINCT category.category)'), count($this->categories) - 1]);
        }



        $query->andFilterWhere(['like', 'company.nameEng', $this->nameEng])
            ->andFilterWhere(['like', 'company.nameRu', $this->nameRu])
            ->andFilterWhere(['between', 'company.created_at', $this->dateStart, $this->dateEnd])
            ->andFilterWhere(['like', 'company.formOfOrganization', $this->formOfOrganization])
            ->andFilterWhere(['like', 'company.officeAdress', $this->officeAdress])
            ->andFilterWhere(['like', 'company.legalAddress', $this->legalAddress])
            ->andFilterWhere(['like', 'company.ogrn', $this->ogrn])
            ->andFilterWhere(['like', 'company.inn', $this->inn])
            ->andFilterWhere(['like', 'company.kpp', $this->kpp])
            ->andFilterWhere(['like', 'company.checkingAccount', $this->checkingAccount])
            ->andFilterWhere(['like', 'company.correspondentAccount', $this->correspondentAccount])
            ->andFilterWhere(['like', 'company.inTheBank', $this->inTheBank])
            ->andFilterWhere(['like', 'company.bik', $this->bik])
            ->andFilterWhere(['like', 'company.okved', $this->okved])
            ->andFilterWhere(['like', 'company.okpo', $this->okpo])
            ->andFilterWhere(['like', 'company.signatoryName', $this->signatoryName])
            ->andFilterWhere(['like', 'company.signatoryMiddleName', $this->signatoryMiddleName])
            ->andFilterWhere(['like', 'company.signatoryLastName', $this->signatoryLastName])
            ->andFilterWhere(['like', 'company.basis', $this->basis])
            ->andFilterWhere(['like', 'company.documentNumber', $this->documentNumber])
            ->andFilterWhere(['like', 'company.description', $this->description]);


        return $dataProvider;
    }
}
