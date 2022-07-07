<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Notification;
use yii\db\Expression;

/**
 * NotificationSearch represents the model behind the search form of `app\models\Notification`.
 */
class NotificationSearch extends Notification
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'consultant_id', 'type'], 'integer'],
            [['title', 'body', 'created_at', 'status'], 'safe'],
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
    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Notification::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 25
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
                            new Expression('FIELD(notification.status, 0,-1,3) ASC'),
                            'created_at' => SORT_ASC
                        ],
                        'desc' => [
                            new Expression('FIELD(notification.status, 0,-1,3) DESC'),
                            'created_at' => SORT_DESC
                        ],
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
            'consultant_id' => $this->consultant_id,
            'type' => $this->type,
            'status' => $this->stringToArray($this->status),
            'created_at' => $this->created_at,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'body', $this->body]);

        return $dataProvider;
    }
}
