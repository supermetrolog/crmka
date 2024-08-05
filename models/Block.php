<?php

namespace app\models;

use app\helpers\JsonFieldNormalizer;
use app\models\ActiveQuery\BlockQuery;
use yii\db\ActiveQuery;
use yii\helpers\Json;

class Block extends oldDb\ObjectsBlock
{
	protected function jsonToArrayStringElements($value): array
	{
		return JsonFieldNormalizer::jsonToArrayStringElements($value);
	}

	protected function jsonToArrayIntElements($value): array
	{
		return JsonFieldNormalizer::jsonToArrayIntElements($value);
	}

	/**
	 * @return array
	 */
	public function getPhotos(): array
	{
		return $this->jsonToArrayStringElements($this->photo_block);
	}

	/**
	 * @return array
	 */
	public function getPurposesBlock(): array
	{
		return $this->jsonToArrayIntElements($this->purposes_block);
	}

	/**
	 * @return array
	 */
	public function getFloors(): array
	{
		return $this->jsonToArrayIntElements($this->floor);
	}

	/**
	 * @return array
	 */
	public function getFloorTypes(): array
	{
		return $this->jsonToArrayIntElements($this->floor_types);
	}

	/**
	 * @return array
	 */
	public function getFirefightingType(): array
	{
		return $this->jsonToArrayIntElements($this->firefighting_type);
	}

	/**
	 * @return array
	 */
	public function getVentilation(): array
	{
		return $this->jsonToArrayIntElements($this->ventilation);
	}

	/**
	 * @return array
	 */
	public function getColumnGrids(): array
	{
		return $this->jsonToArrayIntElements($this->column_grids);
	}

	/**
	 * @return array
	 */
	public function getParts(): array
	{
		return $this->jsonToArrayIntElements($this->parts);
	}

	/**
	 * @return array
	 */
	public function getCranesCatHead(): array
	{
		return $this->jsonToArrayIntElements($this->cranes_cathead);
	}

	/**
	 * @return array
	 */
	public function getCranesOverHead(): array
	{
		return $this->jsonToArrayIntElements($this->cranes_overhead);
	}

	/**
	 * @return array
	 */
	public function getGates(): array
	{
		return $this->jsonToArrayIntElements($this->gates);
	}

	/**
	 * @return array
	 */
	public function getFloorTypesLand(): array
	{
		return $this->jsonToArrayIntElements($this->floor_types_land);
	}

	/**
	 * @return array
	 */
	public function getSafeType(): array
	{
		return $this->jsonToArrayIntElements($this->safe_type);
	}

	/**
	 * @return array
	 */
	public function getLighting(): array
	{
		return $this->jsonToArrayIntElements($this->lighting);
	}

	/**
	 * @return array
	 */
	public function getRackTypes(): array
	{
		return $this->jsonToArrayIntElements($this->rack_types);
	}

	public function getTelphers(): array
	{
		return $this->jsonToArrayIntElements($this->telphers);
	}

	public function getElevators(): array
	{
		return $this->jsonToArrayIntElements($this->elevators);
	}

	public function getCranes(): array
	{
		return $this->jsonToArrayIntElements($this->cranes);
	}

	public function getBuildingLayoutsBlock(): array
	{
		return Json::decode($this->building_layouts_block) ?? [];
	}

	public function getPhotos360Block(): array
	{
		return Json::decode($this->photos_360_block) ?? [];
	}

	public function getBuildingPresentationsBlock(): array
	{
		return Json::decode($this->building_presentations_block) ?? [];
	}

	public function getExcludedAreas(): array
	{
		return Json::decode($this->excluded_areas) ?? [];
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
		$f['lighting']          = function () {
			return $this->getLighting();
		};
		$f['rack_types']        = function () {
			return $this->getRackTypes();
		};
		$f['telphers']          = function () {
			return $this->getTelphers();
		};
		$f['elevators'] = function () {
			return $this->getElevators();
		};
		$f['cranes']    = function () {
			return $this->getCranes();
		};
		$f['building_layouts_block']    = function () {
			return $this->getBuildingLayoutsBlock();
		};
		$f['photos_360_block']    = function () {
			return $this->getPhotos360Block();
		};
		$f['building_presentations_block']    = function () {
			return $this->getBuildingPresentationsBlock();
		};
		$f['excluded_areas']    = function () {
			return $this->getExcludedAreas();
		};
		$f['excluded_areas']    = function () {
			return $this->getExcludedAreas();
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

	public static function find(): BlockQuery
	{
		return new BlockQuery(get_called_class());
	}
}