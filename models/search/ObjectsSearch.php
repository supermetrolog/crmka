<?php

namespace app\models\search;

use app\models\Objects;
use app\models\Search;
use yii\data\ActiveDataProvider;

class ObjectsSearch extends Search
{
    public ?int $complex_id = null;

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['complex_id'], 'integer']
        ];
    }

    /**
     * @return string
     */
    public function formName(): string
    {
        return '';
    }

    /**
     * @param array $params
     * @return ActiveDataProvider
     */
    public function search(array $params): ActiveDataProvider
    {
        $query = Objects::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            $this->getField('complex_id') => $this->complex_id
        ]);

        return $dataProvider;
    }

    /**
     * @return string
     */
    protected function getTableName(): string
    {
        return Objects::tableName();
    }
}