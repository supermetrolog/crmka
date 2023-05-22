<?php

namespace app\components\connector\avito;

use app\components\avito\AvitoValue;
use app\components\interfaces\OfferInterface;
use InvalidArgumentException;
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
                    'url' => $image
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

        return AvitoValue::LEASE_DEPOSIT_NO_DEPOSIT; // TODO: FIX
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
}