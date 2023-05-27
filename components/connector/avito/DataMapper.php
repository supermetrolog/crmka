<?php

namespace app\components\connector\avito;

use app\components\avito\AvitoValue;
use app\components\interfaces\OfferInterface;
use InvalidArgumentException;
use Yii;
use yii\base\ErrorException;
use yii\helpers\ArrayHelper;

class DataMapper
{
    /**
     * @param OfferInterface $offer
     * @return array[]
     */
    public function getImages(OfferInterface $offer): array
    {
        $images = [];

        foreach ($offer->getImages() as $image) {
            $images[] = [
                'tag' => 'Image',
                'value' => '',
                'attributes' => [
                    'url' => Yii::$app->params['url']['objects'] . $image
                ]
            ];
        }

        return $images;
    }

    /**
     * @param OfferInterface $offer
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
        } else if ($deposit < 2) {
            return 1;
        } else if ($deposit < 3) {
            return 2;
        } else {
            return 3;
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
     * @return string
     */
    public function getObjectType(OfferInterface $offer): string
    {
        if ($offer->isLand()) {
            return AvitoValue::OBJECT_TYPE_LAND_INDUSTRIAL;
        }

        if ($offer->isProduction()) {
            return AvitoValue::OBJECT_TYPE_PRODUCTION;
        }

        return AvitoValue::OBJECT_TYPE_WAREHOUSE;
    }

    /**
     * @param OfferInterface $offer
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
     * @param $key
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
     * @return array[]|null
     */
    public function getFloorAdditionally(OfferInterface $offer): ?array
    {
        if (!$offer->hasSeveralFloors()) {
            return null;
        }

        return [[
            'tag' => 'Option',
            'value' => AvitoValue::SEVERAL_FLOORS
        ]];
    }

    /**
     * @param OfferInterface $offer
     * @return string
     */
    public function getHeating(OfferInterface $offer): string
    {
        if (!$offer->hasHeating()) {
            return AvitoValue::HEATING_HAS_NOT;
        }

        switch ($offer->getHeatingType()) {
            case OfferInterface::HEATING_AUTO: return AvitoValue::HEATING_AUTO;
            case OfferInterface::HEATING_CENTRAL: return AvitoValue::HEATING_CENTRAL;
            default: return AvitoValue::HEATING_HAS_NOT;
        }
    }

    /**
     * @param OfferInterface $offer
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
     * @return array|null
     */
    public function getLeasePriceOptions(OfferInterface $offer): ?array
    {
        $res = [];

        if ($offer->isIncludePublicService()) {
            $res[] = [
                'tag' => 'Option',
                'value' => AvitoValue::LEASE_PRICE_OPTION_PUBLIC_SERVICES_INCLUDED
            ];
        }

        if ($offer->isIncludeOPEX()) {
            $res[] = [
                'tag' => 'Option',
                'value' => AvitoValue::LEASE_PRICE_OPTION_OPEX_INCLUDED
            ];
        }

        return $res;
    }
}