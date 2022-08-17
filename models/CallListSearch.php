<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\CallList;
use yii\db\Expression;

/**
 * CallListSearch represents the model behind the search form of `app\models\CallList`.
 */
class CallListSearch extends CallList
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['type'], 'integer'],
            [['id', 'caller_id', 'from', 'to', 'created_at', 'uniqueid', 'call_ended_status', 'updated_at', 'status', 'hangup_timestamp'], 'safe'],
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

    public  function normalizeParams()
    {
        $this->id = $this->stringToArray($this->id);
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
        // $query = CallList::find()->with(['caller', 'phoneFrom.contact', 'phoneTo.contact'])->where(['is not', 'call_ended_status', new Expression('null')]);
        $query = CallList::find()->with(['caller', 'phoneFrom.contact.company', 'phoneTo.contact.company']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 5
            ],
            'sort' => [
                'enableMultiSort' => true,
                'defaultOrder' => [
                    'default' => SORT_DESC,
                ],

                'attributes' => [
                    'created_at',
                    'default' => [
                        'asc' => [
                            new Expression('FIELD(call_list.status, 0,-1,3) ASC'),
                            'created_at' => SORT_ASC
                        ],
                        'desc' => [
                            new Expression('FIELD(call_list.status, 0,-1,3) DESC'),
                            'created_at' => SORT_DESC
                        ],
                    ]
                ]
            ]
        ]);

        $this->load($params, '');
        $this->normalizeParams();
        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'type' => $this->type,
            'created_at' => $this->created_at,
            'status' => $this->stringToArray($this->status),
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'caller_id', $this->caller_id])
            ->andFilterWhere(['like', 'from', $this->from])
            ->andFilterWhere(['like', 'to', $this->to])
            ->andFilterWhere(['like', 'uniqueid', $this->uniqueid])
            ->andFilterWhere(['like', 'call_ended_status', $this->call_ended_status]);

        return $dataProvider;
    }
}
