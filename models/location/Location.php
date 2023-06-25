<?php

namespace app\models\location;

use app\helpers\JsonFieldNormalizer;
use app\models\oldDb;
use yii\db\ActiveQuery;

class Location extends oldDb\location\Location
{
    /**
     * @return array
     */
    public function getHighwayRelevant(): array
    {
        return JsonFieldNormalizer::jsonToArrayWithIntElements($this->highways_relevant);
    }

    /**
     * @return array
     */
    public function getHighwayMoscowRelevant(): array
    {
        return JsonFieldNormalizer::jsonToArrayWithIntElements($this->highways_moscow_relevant);
    }

    /**
     * @return array
     */
    public function getTownsRelevant(): array
    {
        return JsonFieldNormalizer::jsonToArrayWithIntElements($this->towns_relevant);
    }

    /**
     * @return array
     */
    public function getDirectionRelevant(): array
    {
        return JsonFieldNormalizer::jsonToArrayWithIntElements($this->direction_relevant);
    }

    /**
     * @return array
     */
    public function fields(): array
    {
        $fields = parent::fields();
        $fields['highways_relevant'] = function () { return $this->getHighwayRelevant(); };
        $fields['highways_moscow_relevant'] = function () { return $this->getHighwayMoscowRelevant(); };
        $fields['towns_relevant'] = function () { return $this->getTownsRelevant(); };
        $fields['direction_relevant'] = function () { return $this->getDirectionRelevant(); };

        return $fields;
    }

    /**
     * @return ActiveQuery
     */
    public function getRegionRecord(): ActiveQuery
    {
        return $this->hasOne(Region::class, ['id' => 'region']);
    }

    /**
     * @return ActiveQuery
     */
    public function getHighwayRecord(): ActiveQuery
    {
        return $this->hasOne(Highways::class, ['id' => 'highway']);
    }

    /**
     * @return ActiveQuery
     */
    public function getDirectionRecord(): ActiveQuery
    {
        return $this->hasOne(Direction::class, ['id' => 'direction']);
    }

    /**
     * @return ActiveQuery
     */
    public function getDistrictRecord(): ActiveQuery
    {
        return $this->hasOne(District::class, ['id' => 'district']);
    }

    /**
     * @return ActiveQuery
     */
    public function getDistrictMoscowRecord(): ActiveQuery
    {
        return $this->hasOne(DistrictMoscow::class, ['id' => 'district_moscow']);
    }

    /**
     * @return ActiveQuery
     */
    public function getTownRecord(): ActiveQuery
    {
        return $this->hasOne(Town::class, ['id' => 'town']);
    }

    /**
     * @return ActiveQuery
     */
    public function getTownCentralRecord(): ActiveQuery
    {
        return $this->hasOne(TownCentral::class, ['id' => 'town_central']);
    }

    /**
     * @return ActiveQuery
     */
    public function getMetroRecord(): ActiveQuery
    {
        return $this->hasOne(Metro::class, ['id' => 'metro']);
    }
}