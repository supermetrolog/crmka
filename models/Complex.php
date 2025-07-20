<?php

namespace app\models;

use app\helpers\JsonFieldNormalizer;
use app\models\ActiveQuery\ComplexQuery;
use app\models\location\Location;
use app\models\oldDb\User as OldDbUser;
use yii\db\ActiveQuery;
use yii\helpers\Json;

class Complex extends oldDb\Complex
{

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
	public function getInternetType(): array
	{
		return JsonFieldNormalizer::jsonToArrayIntElements($this->internet_type);
	}

	/**
	 * @return array
	 */
	public function getGuardType(): array
	{
		return JsonFieldNormalizer::jsonToArrayIntElements($this->guard_type);
	}

	/**
	 * @return array
	 */
	public function getCranesGantry(): array
	{
		return JsonFieldNormalizer::jsonToArrayIntElements($this->cranes_gantry);
	}

	/**
	 * @return array
	 */
	public function getCranesRailway(): array
	{
		return JsonFieldNormalizer::jsonToArrayIntElements($this->cranes_railway);
	}

	/**
	 * @return array
	 */
	public function getWaterType(): array
	{
		return JsonFieldNormalizer::jsonToArrayIntElements($this->water_type);
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
	public function getBuildingPropertyDocuments(): array
	{
		return Json::decode($this->building_property_documents) ?? [];
	}

	/**
	 * @return array
	 */
	public function getPhotos360(): array
	{
		return Json::decode($this->photos_360) ?? [];
	}

	/**
	 * @return array
	 */
	public function getHeatingAutonomousType(): array
	{
		return JsonFieldNormalizer::jsonToArrayIntElements($this->heating_autonomous_type);
	}

	/**
	 * @return array
	 */
	public function getMixerParts(): array
	{
		return JsonFieldNormalizer::jsonToArrayIntElements($this->mixer_parts);
	}

	/**
	 * @return array
	 */
	public function fields(): array
	{
		$fields = parent::fields();
		unset($fields['photo']);
		$fields['photos']                      = function () {
			return $this->getPhotos();
		};
		$fields['internet_type']               = function () {
			return $this->getInternetType();
		};
		$fields['guard_type']                  = function () {
			return $this->getGuardType();
		};
		$fields['cranes_gantry']               = function () {
			return $this->getCranesGantry();
		};
		$fields['cranes_railway']              = function () {
			return $this->getCranesRailway();
		};
		$fields['water_type']                  = function () {
			return $this->getWaterType();
		};
		$fields['building_layouts']            = function () {
			return $this->getBuildingLayout();
		};
		$fields['building_presentations']      = function () {
			return $this->getBuildingPresentation();
		};
		$fields['heating_autonomous_type']     = function () {
			return $this->getHeatingAutonomousType();
		};
		$fields['mixer_parts']                 = function () {
			return $this->getMixerParts();
		};
		$fields['building_property_documents'] = function () {
			return $this->getBuildingPropertyDocuments();
		};
		$fields['photos_360']                  = function () {
			return $this->getPhotos360();
		};

		return $fields;
	}


	/**
	 * @return array
	 */
	public function extraFields(): array
	{
		$f = parent::extraFields();

		$f['guardTypes']    = 'guardTypes';
		$f['internetTypes'] = 'internetTypes';
		$f['waterTypes']    = 'waterTypes';

		return $f;
	}

	/**
	 * @return ComplexQuery
	 */
	public static function find(): ComplexQuery
	{
		return new ComplexQuery(static::class);
	}

	/**
	 * @return ActiveQuery
	 */
	public function getObjects(): ActiveQuery
	{
		return $this->hasMany(Objects::class, ['complex_id' => 'id']);
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
	public function getOldUser(): ActiveQuery
	{
		return $this->hasOne(OldDbUser::class, ['id' => 'author_id']);
	}

	/**
	 * @return ActiveQuery
	 */
	public function getAuthor(): ActiveQuery
	{
		return $this->hasOne(User::class, ['id' => 'user_id_new'])->via('oldUser');
	}

	/**
	 * @return ActiveQuery
	 */
	public function getAgent(): ActiveQuery
	{
		return $this->hasOne(User::class, ['id' => 'user_id_new'])->via('oldUser');
	}

	/**
	 * @return GuardType[]
	 */
	public function getGuardTypes(): array
	{
		return GuardType::find()->andWhere(['id' => $this->getGuardType()])->all();
	}

	/**
	 * @return InternetType[]
	 */
	public function getInternetTypes(): array
	{
		return InternetType::find()->andWhere(['id' => $this->getInternetType()])->all();
	}

	/**
	 * @return WaterType[]
	 */
	public function getWaterTypes(): array
	{
		return WaterType::find()->andWhere(['id' => $this->getWaterType()])->all();
	}
}