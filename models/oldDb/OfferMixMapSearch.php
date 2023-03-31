<?php

declare(strict_types=1);

namespace app\models\oldDb;

use app\exceptions\ValidationErrorHttpException;
use yii\db\ActiveQuery;
use yii\data\ActiveDataProvider;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

class OfferMixMapSearch extends OfferMixSearch
{
    /**
     *  @var string|array
     */
    public $polygon;

    /**
     * @return array
     */
    public function rules(): array
    {
        return ArrayHelper::merge(parent::rules(), [
            ['polygon', 'string']
        ]);
    }

    /**
     * @return string
     */
    protected function getTableName(): string
    {
        return OfferMix::tableName();
    }

    /**
     * @return ActiveQuery
     */
    private function getQuery(): ActiveQuery
    {
        $select = [
            $this->getField('latitude'),
            $this->getField('longitude'),
            $this->getField('address'),
            $this->getField('complex_id'),
            $this->getField('object_id'),
            $this->getField('type_id'),
            $this->getField('original_id'),
            $this->getField('status'),
            $this->getField('id'),
            $this->getField('photos'),
        ];

        return OfferMix::find()
            ->search()
            ->select($select)
            ->groupBy($this->getField('object_id'));
    }

    /**
     * @return void
     */
    public function normalizePolygon(): void
    {
        $polygon = $this->stringToArray($this->polygon);
        if (!$polygon || !is_array($polygon) || count($polygon) % 2 !== 0) {
            return;
        }
        $coordinates = [];
        for ($i = 0; $i < count($polygon); $i = $i + 2) {
            $coordinates[] = $polygon[$i] . ' ' . $polygon[$i + 1];
        }
        $coordinates[] = $coordinates[0];
        $this->polygon = $coordinates;
    }

    /**
     * @return void
     */
    public function normalizeProps(): void
    {
        parent::normalizeProps();
        $this->normalizePolygon();
    }

    /**
     * @param ActiveQuery $query
     * @return void
     */
    public function setPolygonFilter(ActiveQuery $query): void
    {
        if (!$this->polygon) {
            return;
        }

        $coords = implode(", ", $this->polygon);
        $polygonCondition = <<< EOF
                ST_CONTAINS(
                ST_GEOMFROMTEXT(
                    'POLYGON(
                ($coords)
                )'
                        ),
                        POINT(c_industry_offers_mix.latitude, c_industry_offers_mix.longitude)
                    )
            EOF;
        $query->andWhere(new Expression($polygonCondition));
    }

    /**
     * @param ActiveQuery $query
     * @return void
     * @throws ValidationErrorHttpException
     */
    public function setFilters(ActiveQuery $query): void
    {
        parent::setFilters($query);
        $this->setPolygonFilter($query);
    }

    /**
     * @param array $params
     * @return ActiveDataProvider
     * @throws ValidationErrorHttpException
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
        if (!$this->validate()) {
            throw new ValidationErrorHttpException('SSSS');
        }
        $this->normalizeProps();
        $this->setFilters($query);

        return $dataProvider;
    }
}
