<?php

namespace app\models\letter;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\letter\Letter;

/**
 * LetterSearch represents the model behind the search form of `app\models\letter\Letter`.
 */
class LetterSearch extends Letter
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'user_id', 'status', 'type', 'shipping_method'], 'integer'],
            [['subject', 'body', 'created_at'], 'safe'],
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
        $query = Letter::find()->with(['letterContacts', 'user.userProfile', 'letterEmails', 'letterPhones', 'letterOffers', 'letterWays']);

        // add conditions that should always apply here
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'defaultPageSize' => 50,
                'pageSizeLimit' => [0, 50],
            ],
            'sort' => [
                'enableMultiSort' => true,
                'defaultOrder' => [
                    'created_at' => SORT_DESC
                ],
                'attributes' => [
                    'created_at',
                    'status'
                ],
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
            'user_id' => $this->user_id,
            'created_at' => $this->created_at,
            'status' => $this->status,
            'type' => $this->type,
            'shipping_method' => $this->shipping_method,
        ]);

        $query->andFilterWhere(['like', 'subject', $this->subject])
            ->andFilterWhere(['like', 'body', $this->body]);

        return $dataProvider;
    }
}
