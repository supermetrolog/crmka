<?php

namespace app\models\company\eventslog;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\company\eventslog\CompanyEventsLog;

/**
 * CompanyEventsLogSearch represents the model behind the search form of `app\models\company\eventslog\CompanyEventsLog`.
 */
class CompanyEventsLogSearch extends CompanyEventsLog
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'type', 'company_id', 'user_id'], 'integer'],
            [['message', 'created_at', 'updated_at'], 'safe'],
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
        $query = CompanyEventsLog::find()->with(['user.userProfile']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'defaultPageSize' => 50
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
                            'created_at' => SORT_ASC,
                        ],
                        'desc' => [
                            'created_at' => SORT_DESC
                        ],
                        'default' => SORT_DESC
                    ]
                ]

            ]
        ]);

        $this->load($params, '');

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'type' => $this->type,
            'company_id' => $this->company_id,
            'user_id' => $this->user_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'message', $this->message]);

        return $dataProvider;
    }
}
