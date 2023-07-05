<?php

namespace app\models;

use app\helpers\JsonFieldNormalizer;
use app\models\location\Location;
use yii\db\ActiveQuery;
use yii\helpers\Json;

/**
 * @property ObjectClass $objectClassRecord
 */

class Objects extends oldDb\Objects
{
    /**
     * @return array
     */
    public function getOwners(): array
    {
        return JsonFieldNormalizer::jsonToArrayWithIntElements($this->owners);
    }

    /**
     * @return array
     */
    public function getObjectType(): array
    {
        return JsonFieldNormalizer::jsonToArrayWithIntElements($this->object_type);
    }

    /**
     * @return array
     */
    public function getFloorBuildings(): array
    {
        return JsonFieldNormalizer::jsonToArrayWithIntElements($this->floors_building);
    }

    /**
     * @return array
     */
    public function getPurposes(): array
    {
        return JsonFieldNormalizer::jsonToArrayWithIntElements($this->purposes);
    }

    /**
     * @return array
     */
    public function getCranesGantry(): array
    {
        return JsonFieldNormalizer::jsonToArrayWithIntElements($this->cranes_gantry);
    }

    /**
     * @return array
     */
    public function getCranesRailway(): array
    {
        return JsonFieldNormalizer::jsonToArrayWithIntElements($this->cranes_railway);
    }


    /**
     * @return array
     */
    public function getPhotos(): array
    {
        return Json::decode($this->photo) ?? [];
    }

    /**
     * @return array
     */
    public function getBuildingLayout(): array
    {
        return Json::decode($this->building_layouts) ?? [];
    }

    /**
     * @return array
     */
    public function getBuildingPresentation(): array
    {
        return Json::decode($this->building_presentations) ?? [];
    }

    /**
     * @return array
     */
    public function fields(): array
    {
        $fields = parent::fields();

        $fields['owners'] = function () { return $this->getOwners(); };
        $fields['object_type'] = function () { return $this->getObjectType(); };
        $fields['floors_building'] = function () { return $this->getFloorBuildings(); };
        $fields['purposes'] = function () { return $this->getPurposes(); };
        $fields['object_class_text'] = function () { return $this->objectClassRecord->title; };
        $fields['cranes_gantry'] = function () { return $this->getCranesGantry(); };
        $fields['cranes_railway'] = function () { return $this->getCranesRailway(); };
        $fields['photo'] = function () { return $this->getPhotos(); };
        $fields['building_layouts'] = function () { return $this->getBuildingLayout(); };
        $fields['building_presentations'] = function () { return $this->getBuildingPresentation(); };

        return $fields;
    }

    /**
     * @return array
     */
    public function extraFields(): array
    {
        $f = parent::extraFields();

        $f['purposesRecords'] = 'purposesRecords';
        return $f;
    }

    /**
     * @return array
     */
    public function getPurposesRecords(): array
    {
        return Purposes::find()->andWhere(['id' => $this->getPurposes()])->all();
    }

    /**
     * @return ActiveQuery
     */
    public function getCompany(): ActiveQuery
    {
        return $this->hasOne(Company::class, ['id' => 'company_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getObjectClassRecord(): ActiveQuery
    {
        return $this->hasOne(ObjectClass::class, ['id' => 'object_class']);
    }

    /**
     * @return ActiveQuery
     */
    public function getLocation(): ActiveQuery
    {
        return $this->hasOne(Location::class, ['id' => 'location_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getFirefightingType(): ActiveQuery
    {
        return $this->hasOne(FirefightingType::class, ['id' => 'firefighting_type']);
    }

    /**
     * @return ActiveQuery
     */
    public function getCommercialOffers(): ActiveQuery
    {
        return $this->hasMany(CommercialOffer::class, ['object_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getFloorsRecords(): ActiveQuery
    {
        return $this->hasMany(Floor::class, ['object_id' => 'id']);
    }
}