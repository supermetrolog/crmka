<?php

namespace app\models;

use app\helpers\JsonFieldNormalizer;
use yii\db\ActiveQuery;
use yii\helpers\Json;

class Block extends oldDb\ObjectsBlock
{

    /**
     * @return array
     */
    public function getPhotos(): array
    {
        return Json::decode($this->photo_block) ?? [];
    }

    /**
     * @return array
     */
    public function getPurposesBlock(): array
    {
        return JsonFieldNormalizer::jsonToArrayWithIntElements($this->purposes_block);
    }

    /**
     * @return array
     */
    public function getFloors(): array
    {
        return Json::decode($this->floor) ?? [];
    }

    /**
     * @return array
     */
    public function getFloorTypes(): array
    {
        return JsonFieldNormalizer::jsonToArrayWithIntElements($this->floor_types);
    }

    /**
     * @return array
     */
    public function getFirefightingType(): array
    {
        return JsonFieldNormalizer::jsonToArrayWithIntElements($this->firefighting_type);
    }

    /**
     * @return array
     */
    public function getVentilation(): array
    {
        return JsonFieldNormalizer::jsonToArrayWithIntElements($this->ventilation);
    }

    /**
     * @return array
     */
    public function getColumnGrids(): array
    {
        return JsonFieldNormalizer::jsonToArrayWithIntElements($this->column_grids);
    }

    /**
     * @return array
     */
    public function getParts(): array
    {
        return JsonFieldNormalizer::jsonToArrayWithIntElements($this->parts);
    }

    /**
     * @return array
     */
    public function fields(): array
    {
        $f = parent::fields();

        $f['photos'] = function () { return $this->getPhotos(); };
        $f['purposes_block'] = function () { return $this->getPurposesBlock(); };
        $f['floor'] = function () { return $this->getFloors(); };
        $f['floor_types'] = function () { return $this->getFloorTypes(); };
        $f['firefighting_type'] = function () { return $this->getFirefightingType(); };
        $f['ventilation'] = function () { return $this->getVentilation(); };
        $f['column_grids'] = function () { return $this->getColumnGrids(); };
        $f['parts'] = function () { return $this->getParts(); };

        return $f;
    }

    /**
     * @return array
     */
    public function extraFields(): array
    {
        $f = parent::extraFields();

        $f['floorNumbers'] = 'floorNumbers';
        $f['partsRecords'] = function () { return $this->getPartsRecords()->asArray()->all(); };

        return $f;
    }

    /**
     * @return array
     */
    public function getFloorNumbers(): array
    {
        return FloorNumber::find()->andWhere(['sign' => $this->getFloors()])->all();
    }

    /**
     * @return ActiveQuery
     */
    public function getPartsRecords(): ActiveQuery
    {
        return FloorPart::find()
            ->andWhere(['id' => $this->getParts()])
            ->with(['floor.number']);
    }

    /**
     * @return ActiveQuery
     */
    public function getDeal(): ActiveQuery
    {
        return $this->hasOne(Deal::class, ['original_id' => 'id']);
    }
}