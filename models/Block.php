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
	public function getCranesCatHead(): array
	{
		return JsonFieldNormalizer::jsonToArrayWithIntElements($this->cranes_cathead);
	}

	/**
	 * @return array
	 */
	public function getCranesOverHead(): array
	{
		return JsonFieldNormalizer::jsonToArrayWithIntElements($this->cranes_overhead);
	}

	/**
	 * @return array
	 */
	public function getGates(): array
	{
		return JsonFieldNormalizer::jsonToArrayWithIntElements($this->gates);
	}

	/**
	 * @return array
	 */
	public function getFloorTypesLand(): array
	{
		return JsonFieldNormalizer::jsonToArrayWithIntElements($this->floor_types_land);
	}

	/**
	 * @return array
	 */
	public function getSafeType(): array
	{
		return JsonFieldNormalizer::jsonToArrayWithIntElements($this->safe_type);
	}

	/**
	 * @return array
	 */
	public function getLighting(): array
	{
		return JsonFieldNormalizer::jsonToArrayWithIntElements($this->lighting);
	}

	/**
	 * @return array
	 */
	public function fields(): array
	{
		$f = parent::fields();

		unset($f['photo_block']);

		$f['photos']            = function () {
			return $this->getPhotos();
		};
		$f['purposes_block']    = function () {
			return $this->getPurposesBlock();
		};
		$f['floor']             = function () {
			return $this->getFloors();
		};
		$f['floor_types']       = function () {
			return $this->getFloorTypes();
		};
		$f['firefighting_type'] = function () {
			return $this->getFirefightingType();
		};
		$f['ventilation']       = function () {
			return $this->getVentilation();
		};
		$f['column_grids']      = function () {
			return $this->getColumnGrids();
		};
		$f['parts']             = function () {
			return $this->getParts();
		};
		$f['cranes_cathead']    = function () {
			return $this->getCranesCatHead();
		};
		$f['cranes_overhead']   = function () {
			return $this->getCranesOverHead();
		};
		$f['gates']             = function () {
			return $this->getGates();
		};
		$f['floor_types_land']  = function () {
			return $this->getFloorTypesLand();
		};
		$f['safe_type']         = function () {
			return $this->getSafeType();
		};
		$f['lighting']         = function () {
			return $this->getLighting();
		};

		return $f;
	}

	/**
	 * @return array
	 */
	public function extraFields(): array
	{
		$f = parent::extraFields();

		$f['floorNumbers'] = 'floorNumbers';
		$f['partsRecords'] = function () {
			return $this->getPartsRecords()->asArray()->all();
		};

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