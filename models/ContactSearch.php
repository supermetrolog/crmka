<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Contact;

/**
 * ContactSearch represents the model behind the search form of `app\models\Contact`.
 */
class ContactSearch extends Contact
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'company_id', 'type', 'consultant_id', 'position', 'faceToFaceMeeting', 'warning', 'good', 'status', 'passive_why', 'position_unknown', 'isMain'], 'integer'],
            [['middle_name', 'last_name', 'created_at', 'updated_at', 'first_name', 'passive_why_comment', 'warning_why_comment'], 'safe'],
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
        $query = Contact::find()->joinWith(['emails', 'phones', 'websites', 'wayOfInformings', 'consultant' => function ($query) {
            $query->with(['userProfile']);
        }, 'contactComments' => function ($query) {
            $query->with(['author' => function ($query) {
                $query->with(['userProfile']);
            }]);
        }]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 0,
            ],
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
            'company_id' => $this->company_id,
            'type' => $this->type,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'consultant_id' => $this->consultant_id,
            'position' => $this->position,
            'faceToFaceMeeting' => $this->faceToFaceMeeting,
            'warning' => $this->warning,
            'good' => $this->good,
            'status' => $this->status,
            'passive_why' => $this->passive_why,
            'position_unknown' => $this->position_unknown,
            'isMain' => $this->isMain,
        ]);

        $query->andFilterWhere(['like', 'middle_name', $this->middle_name])
            ->andFilterWhere(['like', 'last_name', $this->last_name])
            ->andFilterWhere(['like', 'first_name', $this->first_name])
            ->andFilterWhere(['like', 'passive_why_comment', $this->passive_why_comment])
            ->andFilterWhere(['like', 'warning_why_comment', $this->warning_why_comment]);

        return $dataProvider;
    }
}
