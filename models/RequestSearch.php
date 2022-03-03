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
    public $rangeMinPricePerFloor;
    public $rangeMaxPricePerFloor;
    public $rangeMinArea;
    public $rangeMaxArea;
    public $rangeMinCeilingHeight;
    public $rangeMaxCeilingHeight;
    public $maxDistanceFromMKAD;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'company_id', 'dealType', 'expressRequest', 'distanceFromMKAD', 'distanceFromMKADnotApplicable', 'minArea', 'maxArea', 'minCeilingHeight', 'maxCeilingHeight', 'firstFloorOnly', 'heated', 'trainLine', 'trainLineLength', 'consultant_id', 'pricePerFloor', 'electricity', 'haveCranes', 'status', 'unknownMovingDate', 'antiDustOnly', 'passive_why', 'rangeMinPricePerFloor', 'rangeMaxPricePerFloor', 'rangeMinArea', 'rangeMaxArea', 'rangeMinCeilingHeight', 'rangeMaxCeilingHeight', 'maxDistanceFromMKAD'], 'integer'],
            [['description', 'created_at', 'updated_at', 'movingDate', 'passive_why_comment', 'all', 'dateStart', 'dateEnd', 'objectTypes'], 'safe'],
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
        $this->objectTypes = $this->stringToArray($this->objectTypes);
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
        $query = Request::find()->distinct()->joinWith(['objectTypes'])->with(['company', 'consultant.userProfile', 'directions', 'districts', 'gateTypes', 'objectClasses', 'regions', 'deal.consultant.userProfile']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 50
            ],
            'sort' => [
                'enableMultiSort' => true,
                'defaultOrder' => [
                    'default' => SORT_DESC
                ],
                'attributes' => [
                    'pricePerFloor',
                    'updated_at',
                    'dealType' => [
                        'asc' => [
                            new Expression('FIELD(request.dealType, 3,1,2,0) ASC')
                        ],
                        'desc' => [
                            new Expression('FIELD(request.dealType, 3,1,2,0) DESC')
                        ]
                    ],
                    'status' => [
                        'asc' => [
                            new Expression('FIELD(request.status, 2,0,1) ASC'),
                        ],
                        'desc' => [
                            new Expression('FIELD(request.status, 2,0,1) DESC'),
                        ],
                    ],
                    'default' => [
                        'asc' => [
                            new Expression('FIELD(request.status, 2,0,1) ASC'),
                            'request.expressRequest' => SORT_ASC,
                            'request.created_at' => SORT_ASC
                        ],
                        'desc' => [
                            new Expression('FIELD(request.status, 2,0,1) DESC'),
                            'request.expressRequest' => SORT_DESC,
                            'request.created_at' => SORT_DESC
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

        // grid filtering conditions
        $query->andFilterWhere([
            'request.id' => $this->id,
            'request.company_id' => $this->company_id,
            'request.dealType' => $this->dealType,
            'request.expressRequest' => $this->expressRequest,
            'request.distanceFromMKAD' => $this->distanceFromMKAD,
            'request.distanceFromMKADnotApplicable' => $this->distanceFromMKADnotApplicable,
            'request.minCeilingHeight' => $this->minCeilingHeight,
            'request.maxCeilingHeight' => $this->maxCeilingHeight,
            'request.firstFloorOnly' => $this->firstFloorOnly,
            'request.heated' => $this->heated,
            'request.minArea' => $this->minArea,
            'request.maxArea' => $this->maxArea,
            'request.heated' => $this->heated,
            'request.trainLine' => $this->trainLine,
            'request.trainLineLength' => $this->trainLineLength,
            'request.consultant_id' => $this->consultant_id,
            'request.pricePerFloor' => $this->pricePerFloor,
            'request.electricity' => $this->electricity,
            'request.haveCranes' => $this->haveCranes,
            'request.status' => $this->status,
            'request.created_at' => $this->created_at,
            'request.updated_at' => $this->updated_at,
            'request.movingDate' => $this->movingDate,
            'request.unknownMovingDate' => $this->unknownMovingDate,
            'request.antiDustOnly' => $this->antiDustOnly,
            'request.passive_why' => $this->passive_why,
            'request_object_type.object_type' => $this->objectTypes,
        ]);
        if ($this->objectTypes && count($this->objectTypes) > 1) {
            $query->groupBy('request.id');
            $query->andFilterHaving(['>', new \yii\db\Expression('COUNT(DISTINCT request_object_type.object_type)'), count($this->objectTypes) - 1]);
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
            ->andFilterWhere(['<=', 'request.distanceFromMKAD', $this->maxDistanceFromMKAD]);

        return $dataProvider;
    }
}
