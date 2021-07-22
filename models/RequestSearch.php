<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Request;

/**
 * RequestSearch represents the model behind the search form of `app\models\Request`.
 */
class RequestSearch extends Request
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'company_id', 'dealType', 'expressRequest', 'distanceFromMKAD', 'distanceFromMKADnotApplicable', 'minArea', 'maxArea', 'minCeilingHeight', 'maxCeilingHeight', 'firstFloorOnly', 'heated', 'antiDustOnly', 'trainLine', 'trainLineLength', 'consultant_id', 'pricePerFloor', 'electricity', 'haveCranes', 'status'], 'integer'],
            [['description', 'created_at', 'updated_at'], 'safe'],
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
        $query = Request::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'company_id' => $this->company_id,
            'dealType' => $this->dealType,
            'expressRequest' => $this->expressRequest,
            'distanceFromMKAD' => $this->distanceFromMKAD,
            'distanceFromMKADnotApplicable' => $this->distanceFromMKADnotApplicable,
            'minArea' => $this->minArea,
            'maxArea' => $this->maxArea,
            'minCeilingHeight' => $this->minCeilingHeight,
            'maxCeilingHeight' => $this->maxCeilingHeight,
            'firstFloorOnly' => $this->firstFloorOnly,
            'heated' => $this->heated,
            'antiDustOnly' => $this->antiDustOnly,
            'trainLine' => $this->trainLine,
            'trainLineLength' => $this->trainLineLength,
            'consultant_id' => $this->consultant_id,
            'pricePerFloor' => $this->pricePerFloor,
            'electricity' => $this->electricity,
            'haveCranes' => $this->haveCranes,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'description', $this->description]);

        return $dataProvider;
    }
}
