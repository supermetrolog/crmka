<?php

namespace app\models;

use app\helpers\ArrayHelper;
use app\helpers\DateTimeHelper;
use app\helpers\JsonFieldNormalizer;
use app\kernel\common\models\AQ\AQ;
use app\models\ActiveQuery\UserQuery;
use app\models\crane\Crane;
use app\models\location\Location;
use Yii;
use yii\db\ActiveQuery;
use yii\helpers\Json;

/**
 * @property ObjectClass $objectClassRecord
 * @property Company     $company
 * @property-read User   $consultant
 */
class Objects extends oldDb\Objects
{
	public bool $rentOrSale      = false;
	public bool $sublease        = false;
	public bool $responseStorage = false;

	/**
	 * @return array
	 */
	public function getOwners(): array
	{
		return JsonFieldNormalizer::jsonToArrayIntElements($this->owners);
	}

	/**
	 * @return array
	 */
	public function getObjectType(): array
	{
		return JsonFieldNormalizer::jsonToArrayIntElements($this->object_type);
	}

	/**
	 * @return array
	 */
	public function getFloorBuildings(): array
	{
		return JsonFieldNormalizer::jsonToArrayIntElements($this->floors_building);
	}

	/**
	 * @return array
	 */
	public function getPurposes(): array
	{
		return JsonFieldNormalizer::jsonToArrayIntElements($this->purposes);
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
	public function getPhotos(): array
	{
		return JsonFieldNormalizer::jsonToArrayStringElements($this->photo);
	}

	/**
	 * @return array
	 */
	public function getBuildingLayout(): array
	{
		return JsonFieldNormalizer::jsonToArrayStringElements($this->building_layouts);
	}

	/**
	 * @return array
	 */
	public function getBuildingPresentation(): array
	{
		return JsonFieldNormalizer::jsonToArrayStringElements($this->building_presentations);
	}

	/**
	 * @return array
	 */
	public function getBuildingOnTerritoryId(): array
	{
		return JsonFieldNormalizer::jsonToArrayIntElements($this->buildings_on_territory_id);
	}

	/**
	 * @return array|mixed
	 */
	public function getInternetType()
	{
		return Json::decode($this->internet_type) ?? [];
	}

	public function getPower(): ?int
	{
		return $this->power !== null ? (int)$this->power : null;
	}

	public function getVideos(): array
	{
		return Json::decode($this->videos) ?? [];
	}

	public function getBuildingPropertyDocuments(): array
	{
		return Json::decode($this->building_property_documents) ?? [];
	}

	public function getPhotos360(): array
	{
		return Json::decode($this->photos_360) ?? [];
	}

	public function getThumb(): ?string
	{
		$photos = $this->getPhotos();

		if ($photos) {
			return Yii::$app->params['url']['objects'] . $photos[0];
		}

		return Yii::$app->params['url']['image_not_found'];
	}

	public function getUpdatedAt(): string
	{
		return DateTimeHelper::fromUnixf($this->last_update > 0 ? $this->last_update : $this->publ_time);
	}

	public function getCreatedAt(): string
	{
		return DateTimeHelper::fromUnixf($this->publ_time);
	}

	/**
	 * @return array
	 */
	public function fields(): array
	{
		$fields = parent::fields();

		$fields['owners']                    = function () {
			return $this->getOwners();
		};
		$fields['object_type']               = function () {
			return $this->getObjectType();
		};
		$fields['floors_building']           = function () {
			return $this->getFloorBuildings();
		};
		$fields['purposes']                  = function () {
			return $this->getPurposes();
		};
		$fields['object_class_text']         = function () {
			return $this->objectClassRecord ? $this->objectClassRecord->title : null;
		};
		$fields['cranes_gantry']             = function () {
			return $this->getCranesGantry();
		};
		$fields['cranes_railway']            = function () {
			return $this->getCranesRailway();
		};
		$fields['photo']                     = function () {
			return $this->getPhotos();
		};
		$fields['building_layouts']          = function () {
			return $this->getBuildingLayout();
		};
		$fields['building_presentations']    = function () {
			return $this->getBuildingPresentation();
		};
		$fields['buildings_on_territory_id'] = function () {
			return $this->getBuildingOnTerritoryId();
		};

		$fields['internet_type']               = function () {
			return $this->getInternetType();
		};
		$fields['power']                       = function () {
			return $this->getPower();
		};
		$fields['videos']                      = function () {
			return $this->getVideos();
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

		$f['purposesRecords']  = 'purposesRecords';
		$f['commercialOffers'] = 'commercialOffers';

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

	/**
	 * @return ActiveQuery
	 */
	public function getCranes(): ActiveQuery
	{
		return $this->hasMany(Crane::class, ['object_id' => 'id']);
	}

	public function getElevatorsRecords(): ActiveQuery
	{
		return $this->hasMany(Elevator::class, ['object_id' => 'id']);
	}

	public function getConsultant(): UserQuery
	{
		/** @var UserQuery */
		return $this->hasOne(User::class, ['user_id_old' => 'agent_id']);
	}

	public function isLand(): bool
	{
		return $this->is_land === 1;
	}

	public function getFloorsCount(): int
	{
		if ($this->isLand()) {
			return 0;
		}

		if (is_null($this->floors)) {
			return ArrayHelper::length($this->getFloorBuildings());
		}

		return $this->floors;
	}

	public static function find(): AQ
	{
		return new AQ(get_called_class());
	}
}
