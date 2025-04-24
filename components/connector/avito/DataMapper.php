<?php

namespace app\components\connector\avito;

use app\components\avito\AvitoFeedGenerator;
use app\components\avito\AvitoValue;
use app\components\interfaces\OfferInterface;
use app\helpers\StringHelper;
use InvalidArgumentException;
use Yii;
use yii\base\ErrorException;
use yii\helpers\ArrayHelper;

class DataMapper
{
	private const WATERMARK_IMAGE_WIDTH = 1200;

	/**
	 * @param OfferInterface $offer
	 *
	 * @return array[]
	 */
	public function getImages(OfferInterface $offer): array
	{
		$images = [];
		$count  = 0;
		foreach ($offer->getImages() as $image) {
			if ($count >= AvitoFeedGenerator::MAX_IMAGES_COUNT) {
				break;
			}

			$count++;

			$images[] = [
				'tag'        => 'Image',
				'value'      => '',
				'attributes' => [
					'url' => $this->generateWatermarkImageUrl($image)
				]
			];
		}

		return $images;
	}

	private function generateWatermarkImageUrl(string $image): string
	{
		if (!StringHelper::startWith($image, '/uploads/objects/')) {
			return $image;
		}

		$imageUrl = StringHelper::after($image, '/uploads/objects/');

		return Yii::$app->params['url']['objects_watermark'] . self::WATERMARK_IMAGE_WIDTH . '/' . $imageUrl;
	}

	/**
	 * @param OfferInterface $offer
	 *
	 * @return string
	 */
	public function getLeaseDeposit(OfferInterface $offer): string
	{
		if (!$offer->hasDeposit()) {
			return AvitoValue::LEASE_DEPOSIT_NO_DEPOSIT;
		}

		$deposit = $offer->getDepositMonth();

		if ($deposit < 1) {
			return AvitoValue::LEASE_DEPOSIT_NO_DEPOSIT;
		} else {
			if ($deposit < 2) {
				return 1;
			} else {
				if ($deposit < 3) {
					return 2;
				} else {
					return 3;
				}
			}
		}
	}

	/**
	 * @throws ErrorException
	 */
	public function getRentalType(OfferInterface $offer): string
	{
		if ($offer->isRentType()) {
			return AvitoValue::RENTAL_TYPE_DIRECT;
		}

		if ($offer->isSubleaseType()) {
			return AvitoValue::RENTAL_TYPE_SUBLEASE;
		}

		throw new ErrorException('Offer is not rental type');
	}


	/**
	 * @param OfferInterface $offer
	 *
	 * @return string
	 */
	public function getObjectType(OfferInterface $offer): string
	{
		if ($offer->isLand()) {
			return AvitoValue::OBJECT_TYPE_LAND_INDUSTRIAL;
		}

		if ($offer->isWarehouse()) {
			return AvitoValue::OBJECT_TYPE_WAREHOUSE;
		}

		if ($offer->isProduction()) {
			return AvitoValue::OBJECT_TYPE_PRODUCTION;
		}

		return AvitoValue::OBJECT_TYPE_WAREHOUSE;
	}

	/**
	 * @param OfferInterface $offer
	 *
	 * @return string
	 */
	public function getCategory(OfferInterface $offer): string
	{
		if ($offer->isLand()) {
			return AvitoValue::CATEGORY_LAND;
		}

		return AvitoValue::CATEGORY_COMMERCIAL_OBJECT;
	}

	/**
	 * @param array $options
	 * @param       $key
	 *
	 * @return mixed
	 */
	private function getValueOrThrow(array $options, $key)
	{
		if (ArrayHelper::keyExists($key, $options)) {
			return $options[$key];
		}

		throw new InvalidArgumentException("Key: $key not exists in array");
	}

	/**
	 * @param OfferInterface $offer
	 *
	 * @return string|null
	 */
	public function getRentalHolidays(OfferInterface $offer): ?string
	{
		if ($offer->hasRentalHolidays()) {
			return AvitoValue::RENTAL_HOLIDAYS_HAS;
		}

		return null;
	}

	/**
	 * @param OfferInterface $offer
	 *
	 * @return string
	 */
	public function getFloor(OfferInterface $offer): string
	{
		if ($offer->getFloorMax() < 0) {
			return AvitoValue::FLOOR_BASEMENT;
		}

		return $offer->getFloorMax();
	}

	/**
	 * @param OfferInterface $offer
	 *
	 * @return array[]|null
	 */
	public function getFloorAdditionally(OfferInterface $offer): ?array
	{
		if (!$offer->hasSeveralFloors()) {
			return null;
		}

		return [[
			        'tag'   => 'Option',
			        'value' => AvitoValue::SEVERAL_FLOORS
		        ]];
	}

	/**
	 * @param OfferInterface $offer
	 *
	 * @return string
	 */
	public function getHeating(OfferInterface $offer): string
	{
		if (!$offer->hasHeating()) {
			return AvitoValue::HEATING_HAS_NOT;
		}

		switch ($offer->getHeatingType()) {
			case OfferInterface::HEATING_AUTO:
				return AvitoValue::HEATING_AUTO;
			case OfferInterface::HEATING_CENTRAL:
				return AvitoValue::HEATING_CENTRAL;
			default:
				return AvitoValue::HEATING_HAS_NOT;
		}
	}

	/**
	 * @param OfferInterface $offer
	 *
	 * @return string|null
	 */
	public function getBuildingClass(OfferInterface $offer): ?string
	{
		if (in_array($offer->getClass(), [AvitoValue::BUILDING_CLASS_A, AvitoValue::BUILDING_CLASS_B, AvitoValue::BUILDING_CLASS_C, AvitoValue::BUILDING_CLASS_D])) {
			return $offer->getClass();
		}

		return null;
	}

	/**
	 * @param OfferInterface $offer
	 *
	 * @return array|null
	 */
	public function getLeasePriceOptions(OfferInterface $offer): ?array
	{
		$res = [];

		if ($offer->isIncludePublicService()) {
			$res[] = [
				'tag'   => 'Option',
				'value' => AvitoValue::LEASE_PRICE_OPTION_PUBLIC_SERVICES_INCLUDED
			];
		}

		if ($offer->isIncludeOPEX()) {
			$res[] = [
				'tag'   => 'Option',
				'value' => AvitoValue::LEASE_PRICE_OPTION_OPEX_INCLUDED
			];
		}

		return $res;
	}

	/**
	 * @param OfferInterface $offer
	 *
	 * @return string|null
	 */
	public function getSquareAdditionally(OfferInterface $offer): ?string
	{
		if ($offer->isSolid()) {
			return null;
		}

		return AvitoValue::SQUARE_ADDITIONAL_POSSIBLE_CUTTING;
	}

	/**
	 * @param OfferInterface $offer
	 *
	 * @return string
	 */
	public function getOperationType(OfferInterface $offer): string
	{
		if ($offer->isRentType() || $offer->isSubleaseType()) {
			return AvitoValue::OPERATION_TYPE_RENT;
		}

		return AvitoValue::OPERATION_TYPE_SALE;
	}

	/**
	 * @param OfferInterface $offer
	 *
	 * @return float
	 */
	public function getPrice(OfferInterface $offer): float
	{
		$priceForAllArea = $offer->getMaxPrice() * $offer->getMaxArea();

		if ($offer->isRentType() || $offer->isSubleaseType()) {
			return $priceForAllArea / 12;
		}

		return $priceForAllArea;
	}
}