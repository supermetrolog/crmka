<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\FavoriteOffer;
use app\models\oldDb\OfferMix;
use yii\db\Expression;

/**
 * FavoriteOfferSearch represents the model behind the search form of `app\models\FavoriteOffer`.
 */
class FavoriteOfferSearch extends FavoriteOffer
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'user_id', 'complex_id', 'object_id', 'original_id'], 'integer'],
            [['created_at'], 'safe'],
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
    private function getDsnAttribute($name, $dsn)
    {
        if (preg_match('/' . $name . '=([^;]*)/', $dsn, $match)) {
            return $match[1];
        } else {
            return null;
        }
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
        $joinedDbName = $this->getDsnAttribute('dbname', OfferMix::getDb()->dsn);

        $query = FavoriteOffer::find()->distinct()->joinWith(['offer' => function ($query) use ($joinedDbName) {
            return $query->from($joinedDbName . ".c_industry_offers_mix");
        }])->where(['is not', $joinedDbName . '.c_industry_offers_mix.id', new Expression("null")])
            ->andWhere([$joinedDbName . '.c_industry_offers_mix.status' => 1])
            ->andWhere(['!=', $joinedDbName . '.c_industry_offers_mix.deleted', 1]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'defaultPageSize' => 0,
                'pageSizeLimit' => [0, 50],
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
            'user_id' => $this->user_id,
            'complex_id' => $this->complex_id,
            'object_id' => $this->object_id,
            'original_id' => $this->original_id,
            'created_at' => $this->created_at,
        ]);

        return $dataProvider;
    }
}
