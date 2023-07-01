<?php

namespace app\models;

use app\helpers\JsonFieldNormalizer;
use app\models\location\Location;
use yii\db\ActiveQuery;

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
    public function fields(): array
    {
        $fields = parent::fields();

        $fields['owners'] = function () { return $this->getOwners(); };
        $fields['object_type'] = function () { return $this->getObjectType(); };
        $fields['floors_building'] = function () { return $this->getFloorBuildings(); };
        $fields['purposes'] = function () { return $this->getPurposes(); };
        $fields['object_class_text'] = function () { return $this->objectClassRecord->title; };

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
}