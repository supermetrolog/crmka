<?php

namespace app\models;

use app\helpers\JsonFieldNormalizer;

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

        return $fields;
    }
}