<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Company;

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
            [['id', 'noName', 'companyGroup_id', 'status', 'consultant_id', 'broker_id', 'activityGroup', 'activityProfile', 'active', 'formOfOrganization', 'processed', 'passive_why', 'rating'], 'integer'],
            [['all', 'nameEng', 'nameRu', 'officeAdress', 'legalAddress', 'ogrn', 'inn', 'kpp', 'checkingAccount', 'correspondentAccount', 'inTheBank', 'bik', 'okved', 'okpo', 'signatoryName', 'signatoryMiddleName', 'signatoryLastName', 'basis', 'documentNumber', 'description', 'created_at', 'updated_at', 'passive_why_comment', 'categories', 'dateStart', 'dateEnd'], 'safe'],
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

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Company::find()->distinct()->joinWith(['requests', 'categories', 'contacts' => function ($query) {
            $query->joinWith(['phones'])->with(['emails', 'contactComments']);
        }, 'categories'])->with([
            'requests' => function ($query) {
                $query->where(['request.status' => Request::STATUS_ACTIVE]);
            },
            'companyGroup', 'broker', 'deals', 'consultant' => function ($query) {
                $query->with('userProfile');
            }, 'productRanges'
        ]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 50,
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
                            new \yii\db\Expression('case when request.status = 1 then request.created_at else NULL end ASC'),
                            'company.rating' => SORT_ASC,
                            'company.created_at' => SORT_ASC
                        ],
                        'desc' => [
                            new \yii\db\Expression('case when NOW() BETWEEN company.created_at AND DATE_ADD(company.created_at, INTERVAL 12 HOUR) then company.created_at else NULL end DESC'),
                            new \yii\db\Expression('case when request.status = 1 then request.created_at else NULL end DESC'),
                            'company.rating' => SORT_DESC,
                            'company.created_at' => SORT_DESC
                        ],
                        'default' => SORT_DESC,
                    ],
                ],


            ]
        ]);

        $this->load($params, '');

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // return explode(",", $this->categories);
        // grid filtering conditions
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
            'category.category' => $this->categories ? explode(",", $this->categories) : null
        ]);
        $query->andFilterWhere(['like', 'company.nameEng', $this->nameEng])
            ->andFilterWhere(['like', 'company.nameRu', $this->nameRu])
            ->andFilterWhere(['between', 'company.created_at', $this->dateStart, $this->dateEnd ?? date('Y-m-d')])
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


        $query->orFilterWhere(['company.id' => $this->all])
            ->orFilterWhere(['like', 'contact.first_name', $this->all])
            ->orFilterWhere(['like', 'contact.middle_name', $this->all])
            ->orFilterWhere(['like', 'contact.last_name', $this->all])
            ->orFilterWhere(['like', 'phone.phone', $this->all])
            ->orFilterWhere(['like', 'company.nameEng', $this->all])
            ->orFilterWhere(['like', 'company.nameRu', $this->all]);

        return $dataProvider;
    }
}
