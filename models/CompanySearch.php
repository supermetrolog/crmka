<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Company;
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

        $query = Company::find()->distinct()->joinWith(['requests', 'categories', 'contacts' => function ($query) {
            $query->joinWith(['phones'])->with(['emails', 'contactComments']);
        }, 'categories'])->with([
            'requests' => function ($query) {
                $query->where(['request.status' => Request::STATUS_ACTIVE]);
            },
            'companyGroup', 'broker', 'deals', 'dealsRequestEmpty', 'consultant.userProfile', 'productRanges',
            'mainContact.emails', 'mainContact.phones'
        ]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'defaultPageSize' => 50,
                'pageSizeLimit' => [0, 50],
            ],
            'sort' => [
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
                            // new \yii\db\Expression('case when request.status = 1 then request.created_at else NULL end ASC'),
                            new Expression('(IF ((SELECT EXISTS(SELECT r.status FROM request as r WHERE r.company_id = company.id AND r.status = 1 LIMIT 1)), IF(request.updated_at IS NOT NULL, request.updated_at, request.created_at), NULL)) ASC'),
                            'request.status' => SORT_ASC,
                            'company.rating' => SORT_ASC,
                            'company.status' => SORT_ASC,
                            'company.created_at' => SORT_ASC
                        ],
                        'desc' => [
                            new \yii\db\Expression('case when NOW() BETWEEN company.created_at AND DATE_ADD(company.created_at, INTERVAL 12 HOUR) then company.created_at else NULL end DESC'),
                            // new \yii\db\Expression('case when request.status = 1 then request.created_at else NULL end DESC'),
                            new Expression('(IF ((SELECT EXISTS(SELECT r.status FROM request as r WHERE r.company_id = company.id AND r.status = 1 LIMIT 1)), IF(request.updated_at IS NOT NULL, request.updated_at, request.created_at), NULL)) DESC'),
                            'request.status' => SORT_DESC,
                            'company.rating' => SORT_DESC,
                            'company.status' => SORT_DESC,
                            'company.created_at' => SORT_DESC
                        ],
                        'default' => SORT_DESC,
                    ],
                ],


            ]
        ]);

        $this->load($params, '');
        $this->normalizeProps();
        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // return explode(",", $this->categories);
        // grid filtering conditions
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
                    IF (`company`.`id` LIKE '%{$this->all}%', 90, 0) 
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
