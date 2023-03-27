<?php

declare(strict_types=1);

namespace app\models\oldDb;

use yii\db\ActiveQuery;
use yii\data\ActiveDataProvider;

class OfferMixMapSearch extends OfferMixSearch
{
    protected function getTableName(): string
    {
        return OfferMix::tableName();
    }

    private function getQuery(): ActiveQuery
    {
        $select = [
            $this->getField('latitude'),
            $this->getField('longitude'),
            $this->getField('address'),
            $this->getField('complex_id'),
            $this->getField('status'),
            $this->getField('id'),
        ];

        return OfferMix::find()
            ->search()
            ->select($select)
            ->asArray();
    }

    /**
     * @param  array $params
     * @return ActiveDataProvider
     */
    public function search(array $params): ActiveDataProvider
    {
        $query = $this->getQuery();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'defaultPageSize' => 50,
                'pageSizeLimit' => [0, 50],
            ],
        ]);

        $this->load($params, '');
        $this->normalizeProps();

        $this->setFilters($query);

        return $dataProvider;
    }
}
