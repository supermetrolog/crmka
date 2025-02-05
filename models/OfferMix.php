<?php

namespace app\models;

use app\components\interfaces\OfferInterface;
use app\models\ActiveQuery\ChatMemberQuery;
use app\models\ActiveQuery\ComplexQuery;
use app\models\ActiveQuery\ObjectChatMemberQuery;
use app\models\ActiveQuery\OfferMixQuery;
use Throwable;
use Yii;
use yii\base\ErrorException;
use yii\helpers\Json;

/**
 * @property ChatMember $chatMember
 * @property Complex    $complex
 */
class OfferMix extends oldDb\OfferMix implements OfferInterface
{
	public const DELETED_TRUE_VALUE      = 1;
	public const DELETED_FALSE_VALUE     = 0;
	public const HEATING_CENTRAL_STRING  = 'Центральное';
	public const HEATING_AUTO_STRING     = 'Автономное';
	public const OPEX_INCLUDED           = 1;
	public const PUBLIC_SERVICE_INCLUDED = 1;

	public static function getMorphClass(): string
	{
		return 'offer_mix';
	}

	/**
	 * @return bool
	 */
	public function isSaleType(): bool
	{
		return $this->deal_type === self::DEAL_TYPE_SALE;
	}

	/**
	 * @return bool
	 */
	public function isRentType(): bool
	{
		return $this->deal_type === self::DEAL_TYPE_RENT;
	}

	/**
	 * @return bool
	 */
	function isResponseStorageType(): bool
	{
		return $this->deal_type === self::DEAL_TYPE_RESPONSE_STORAGE;
	}

	/**
	 * @return bool
	 */
	public function isSubleaseType(): bool
	{
		return $this->deal_type === self::DEAL_TYPE_SUBLEASE;
	}

	/**
	 * @return OfferMixQuery
	 */
	public static function find(): OfferMixQuery
	{
		return new OfferMixQuery(get_called_class());
	}

	/**
	 * @return string
	 */
	function getVisibleID(): string
	{
		return $this->visual_id;
	}

	/**
	 * @return int
	 */
	function getVisualId(): string
	{
		return $this->visual_id;
	}

	/**
	 * @return bool
	 */
	public function isBlock(): bool
	{
		return $this->type_id === self::MINI_TYPE_ID;
	}

	/**
	 * @return bool
	 */
	public function isGeneral(): bool
	{
		return $this->type_id === self::GENERAL_TYPE_ID;
	}

	/**
	 * @return bool
	 */
	public function isObject(): bool
	{
		return $this->type_id === self::OBJECT_TYPE_ID;
	}

	/**
	 * @return string
	 */
	function getDescription(): string
	{
		if ($this->isGeneral()) {
			return $this->description ?? '';
		}

		try {
			if ($this->isBlock() || $this->block || !$this->block->description_manual_use) {
				$url = Yii::$app->params['url']['objects'] . 'autodesc.php/' . $this->original_id . '/' . $this->type_id . '?api=1';

				return file_get_contents($url);
			} else {
				return $this->block->description;
			}
		} catch (Throwable $th) {
			return '';
		}
	}

	/**
	 * @return string
	 */
	function getAddress(): string
	{
		return $this->address;
	}

	/**
	 * @return string
	 */
	function getLatitude(): string
	{
		return $this->latitude;
	}

	/**
	 * @return string
	 */
	function getLongitude(): string
	{
		return $this->longitude;
	}

	/**
	 * @return array
	 */
	function getObjectTypes(): array
	{
		return Json::decode($this->object_type) ?? [];
	}

	/**
	 * @return bool
	 */
	function isWarehouse(): bool
	{
		return in_array(self::OBJECT_TYPE_WAREHOUSE, $this->getObjectTypes());
	}

	/**
	 * @return bool
	 */
	function isProduction(): bool
	{
		return in_array(self::OBJECT_TYPE_PRODUCTION, $this->getObjectTypes());
	}

	/**
	 * @return bool
	 */
	public function isLandObjectType(): bool
	{
		return in_array(self::OBJECT_TYPE_LAND, $this->getObjectTypes());
	}

	/**
	 * @return bool
	 */
	public function isLand(): bool
	{
		return !!$this->is_land;
	}

	/**
	 * @return bool
	 */
	public function hasDeposit(): bool
	{
		return !!$this->deposit;
	}

	/**
	 * @return float
	 */
	public function getDepositMonth(): float
	{
		if ($this->isBlock()) {
			return $this->offer->deposit_value ?? 0;
		}

		if ($this->isGeneral()) {
			$max = 0;
			foreach ($this->miniOffersMix as $miniOffer) {
				if (($miniOffer->offer->deposit_value ?? 0) > $max) {
					$max = $miniOffer->offer->deposit_value;
				}
			}

			return $max;
		}

		return 0;
	}

	/**
	 * @return string
	 */
	public function getFullConsultantName(): string
	{
		return $this->consultant->userProfile->getFullName();
	}

	/**
	 * @return string
	 */
	public function getContactPhone(): string
	{
		return Yii::$app->params['company_phone'];
	}

	/**
	 * @return string[]
	 */
	public function getImages(): array
	{
		return Json::decode($this->photos);
	}

	/**
	 * @return float
	 */
	public function getCeilingHeightMin(): float
	{
		return min($this->ceiling_height_min, $this->ceiling_height_max);
	}

	/**
	 * @return float
	 */
	public function getCeilingHeightMax(): float
	{
		return max($this->ceiling_height_min, $this->ceiling_height_max);
	}

	/**
	 * @return float
	 */
	public function getPower(): float
	{
		if ($this->isBlock()) {
			if (!$this->block) {
				return 0;
			}

			return (float)$this->block->power;
		}

		if ($this->isGeneral()) {
			$power = 0;
			if (!$this->miniOffersMix) {
				return 0;
			}
			foreach ($this->miniOffersMix as $miniOffer) {
				if (!$miniOffer->block) {
					return 0;
				}
				$power += (float)$miniOffer->block->power;
			}

			return $power;
		}

		return $this->power;
	}

	/**
	 * @return float
	 */
	public function getPowerCapacity(): float
	{
		return $this->getPower();
	}

	/**
	 * @return bool
	 */
	public function hasRentalHolidays(): bool
	{
		return !!$this->holidays;
	}

	/**
	 * @return int
	 */
	public function getFloorMin(): int
	{
		return min($this->floor_min, $this->floor_max);
	}

	/**
	 * @return int
	 */
	public function getFloorMax(): int
	{
		return max($this->floor_min, $this->floor_max);
	}

	/**
	 * @return bool
	 */
	public function hasSeveralFloors(): bool
	{
		return $this->floor_min !== $this->floor_max;
	}

	/**
	 * @return int
	 */
	public function getHeatingType(): int
	{
		if ($this->complex->heating_central) {
			return self::HEATING_CENTRAL;
		}

		if ($this->complex->heating_autonomous) {
			return self::HEATING_AUTO;
		}

		return 0;
	}

	/**
	 * @return string
	 */
	public function getClass(): string
	{
		return $this->class_name;
	}

	/**
	 * @return bool
	 */
	public function isIncludeOPEX(): bool
	{
		return $this->price_opex === self::OPEX_INCLUDED;
	}

	/**
	 * @return bool
	 */
	public function isIncludePublicService(): bool
	{
		return $this->public_services === self::PUBLIC_SERVICE_INCLUDED;
	}

	/**
	 * @return float
	 */
	public function getMaxRentPrice(): float
	{
		return max($this->price_floor_min, $this->price_floor_max) ?? 0;
	}

	/**
	 * @return float
	 */
	public function getMaxSalePrice(): float
	{
		return max($this->price_sale_min, $this->price_sale_max) ?? 0;
	}

	/**
	 * @return float
	 */
	public function getMaxPrice(): float
	{
		if ($this->isRentType() || $this->isSubleaseType()) {
			return $this->getMaxRentPrice();
		}

		return $this->getMaxSalePrice();
	}

	/**
	 * @return float
	 */
	public function getMaxArea(): float
	{
		return max($this->area_min, $this->area_max) ?? 0;
	}

	/**
	 * @return float
	 */
	public function getMaxAreaPerSotka(): float
	{
		return $this->getMaxArea() / 100;
	}

	/**
	 * @return bool
	 */
	public function isSolid(): bool
	{
		if ($this->isBlock() && $this->block) {
			return !!$this->block->is_solid;
		}

		if ($this->isGeneral()) {
			return true;
		}

		return false;
	}

	/**
	 * @return string
	 */
	public function getAvitoAdStartDate(): string
	{
		if ($this->isBlock() && $this->block) {
			return $this->block->ad_avito_date_start;
		}

		return '';
	}

	/**
	 * @return string
	 */
	public function getUniqueId(): string
	{
		return $this->original_id . '-' . $this->getVisualId();
	}

	/**
	 * @return ObjectChatMemberQuery
	 * @throws ErrorException
	 */
	public function getObjectChatMember(): ObjectChatMemberQuery
	{
		/** @var ObjectChatMemberQuery $query */
		$query = $this->hasOne(ObjectChatMember::class, ['object_id' => 'object_id'])->from(ObjectChatMember::getTable());

		return $query;
	}

	/**
	 * @return ChatMemberQuery
	 * @throws ErrorException
	 */
	public function getChatMember(): ChatMemberQuery
	{
		/** @var ChatMemberQuery $query */
		$query = $this->hasOne(ChatMember::class, ['model_id' => 'id'])
		              ->andOnCondition(['model_type' => ObjectChatMember::getMorphClass()])
		              ->via('objectChatMember')
		              ->from(ChatMember::getTable());

		return $query;
	}

	public function getComplex(): ComplexQuery
	{
		/** @var ComplexQuery $query */
		$query = $this->hasOne(Complex::class, ['id' => 'complex_id']);

		return $query;
	}


	public function isDeleted(): bool
	{
		return $this->deleted === self::DELETED_TRUE_VALUE;
	}
}