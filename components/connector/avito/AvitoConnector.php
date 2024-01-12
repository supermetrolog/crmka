<?php

namespace app\components\connector\avito;

use app\components\avito\AvitoObject;
use app\components\avito\AvitoParam;
use app\components\avito\AvitoValue;
use app\components\interfaces\OfferInterface;
use yii\base\ErrorException;

class AvitoConnector
{
    /** @var OfferInterface[]  */
    private array $data;

    private DataMapper $dataMapper;

    /**
     * @param OfferInterface[] $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
        $this->dataMapper = new DataMapper();
    }

    /**
     * @return array<AvitoObject[]>
     * @throws ErrorException
     */
    public function getData(): array
    {
        $data = [];

        foreach ($this->data as $offer) {
            // TODO: implemented avito connector for response storage deal type
            if ($offer->isResponseStorageType()) {
                continue;
            }

            $offerData = $this->getGeneralData($offer);

            if ($offer->isRentType() || $offer->isSubleaseType()) {
                $offerData = [...$offerData, ...$this->getDataForRent($offer)];
            }

            if ($offer->isSaleType()) {
                $offerData = [...$offerData, ...$this->getDataForSale($offer)];
            }

            $data[] = $offerData;
        }

        return $this->clean($data);
    }

    /**
     * @param array<AvitoObject[]> $data
     * @return array
     */
    private function clean(array $data): array
    {
        $cleanedData = [];
        foreach ($data as $objects) {
            $cleanedObjects = [];
            foreach ($objects as $object) {
                if (!is_null($object->value)) {
                    $cleanedObjects[] = $object;
                }
            }

            $cleanedData[] = $cleanedObjects;
        }

        return $cleanedData;
    }

    /**
     * @param OfferInterface $offer
     * @return array
     */
    private function getGeneralData(OfferInterface $offer): array
    {
        return [
            new AvitoObject(AvitoParam::ID, $offer->getUniqueId()),
            new AvitoObject(AvitoParam::OPERATION_TYPE, $this->dataMapper->getOperationType($offer)),
            new AvitoObject(AvitoParam::DESCRIPTION, $offer->getDescription()),
//            new AvitoObject(AvitoParam::DATE_BEGIN, $offer->getAvitoAdStartDate()),
            new AvitoObject(AvitoParam::ADDRESS, $offer->getAddress()),
            new AvitoObject(AvitoParam::LATITUDE, $offer->getLatitude()),
            new AvitoObject(AvitoParam::LONGITUDE, $offer->getLongitude()),
            new AvitoObject(AvitoParam::CATEGORY,  $this->dataMapper->getCategory($offer)),
            new AvitoObject(AvitoParam::PRICE, $this->dataMapper->getPrice($offer)),
            new AvitoObject(AvitoParam::OBJECT_TYPE, $this->dataMapper->getObjectType($offer)),
            new AvitoObject(AvitoParam::PROPERTY_RIGHTS, AvitoValue::PROPERTY_RIGHT_AGENT),
            new AvitoObject(AvitoParam::MANAGER_NAME, $offer->getFullConsultantName()),
            new AvitoObject(AvitoParam::CONTACT_PHONE, $offer->getContactPhone()),
            new AvitoObject(AvitoParam::IMAGES, $this->dataMapper->getImages($offer)),
//            new AvitoObject(AvitoParam::AD_STATUS, 'Highlight'), // TODO: for future
            new AvitoObject(AvitoParam::CONTACT_METHOD, AvitoValue::CONTACT_METHOD_PHONE_AND_MESSAGES),
            new AvitoObject(AvitoParam::RENTAL_HOLIDAYS,  $this->dataMapper->getRentalHolidays($offer)),
        ];
    }

    /**
     * @param OfferInterface $offer
     * @return AvitoObject[]
     * @throws ErrorException
     */
    private function getDataForRent(OfferInterface $offer): array
    {
        if ($offer->isLand()) {
            return $this->getDataForLandRent($offer);
        }

        return $this->getDataForObjectRent($offer);
    }

    /**
     * @param OfferInterface $offer
     * @return AvitoObject[]
     * @throws ErrorException
     */
    private function getDataForObjectRent(OfferInterface $offer): array
    {
        return [
            new AvitoObject(AvitoParam::RENTAL_TYPE, $this->dataMapper->getRentalType($offer)),
            new AvitoObject(AvitoParam::LEASE_DEPOSIT,  $this->dataMapper->getLeaseDeposit($offer)),
            new AvitoObject(AvitoParam::LEASE_COMMISSION_SIZE, 0),
            new AvitoObject(AvitoParam::ENTRANCE, AvitoValue::ENTRANCE_FROM_STREET),
            new AvitoObject(AvitoParam::FLOOR, $this->dataMapper->getFloor($offer)),
            new AvitoObject(AvitoParam::FLOOR_ADDITIONALLY, $this->dataMapper->getFloorAdditionally($offer)),
            new AvitoObject(AvitoParam::PARKING_TYPE, AvitoValue::PARKING_TYPE_IN_THE_STREET),
            new AvitoObject(AvitoParam::SQUARE, $offer->getMaxArea()),
            new AvitoObject(AvitoParam::SQUARE_ADDITIONALLY, $this->dataMapper->getSquareAdditionally($offer)),
//            new AvitoObject(AvitoParam::PRICE_TYPE, AvitoValue::PRICE_TYPE_PER_MONTH_PER_SQUARE_METER), // TODO: fix
            new AvitoObject(AvitoParam::CEILING_HEIGHT, $offer->getCeilingHeightMin()),
            new AvitoObject(AvitoParam::HEATING,  $this->dataMapper->getHeating($offer)),
            new AvitoObject(AvitoParam::BUILDING_CLASS, $this->dataMapper->getBuildingClass($offer)),
            new AvitoObject(AvitoParam::LEASE_PRICE_OPTIONS, $this->dataMapper->getLeasePriceOptions($offer)),
            new AvitoObject(AvitoParam::ENTRANCE_ADDITIONALLY, AvitoValue::ENTRANCE_ADDITIONALLY_SEPARATE),
            new AvitoObject(AvitoParam::BUILDING_TYPE, AvitoValue::BUILDING_TYPE_OTHER),
        ];
    }

    /**
     * @param OfferInterface $offer
     * @return AvitoObject[]
     */
    private function getDataForLandRent(OfferInterface $offer): array
    {
        return [
            new AvitoObject(AvitoParam::LAND_AREA, $offer->getMaxAreaPerSotka()),
            new AvitoObject(AvitoParam::LEASE_COMMISSION_SIZE, 0),
            new AvitoObject(AvitoParam::LEASE_DEPOSIT,  $this->dataMapper->getLeaseDeposit($offer)),
        ];
    }

    /**
     * @param OfferInterface $offer
     * @return AvitoObject[]
     */
    private function getDataForSale(OfferInterface $offer): array
    {
        if ($offer->isLand()) {
            return $this->getDataForLandSale($offer);
        }

        return $this->getDataForObjectSale($offer);

    }


    /**
     * @param OfferInterface $offer
     * @return AvitoObject[]
     */
    private function getDataForObjectSale(OfferInterface $offer): array
    {
        return [
            new AvitoObject(AvitoParam::TRANSACTION_TYPE, AvitoValue::TRANSACTION_TYPE_SALE),
            new AvitoObject(AvitoParam::ENTRANCE, AvitoValue::ENTRANCE_FROM_STREET),
            new AvitoObject(AvitoParam::FLOOR, $this->dataMapper->getFloor($offer)),
            new AvitoObject(AvitoParam::FLOOR_ADDITIONALLY, $this->dataMapper->getFloorAdditionally($offer)),
            new AvitoObject(AvitoParam::PARKING_TYPE, AvitoValue::PARKING_TYPE_IN_THE_STREET),
            new AvitoObject(AvitoParam::SQUARE, $offer->getMaxArea()),
            new AvitoObject(AvitoParam::SQUARE_ADDITIONALLY, $this->dataMapper->getSquareAdditionally($offer)),
            new AvitoObject(AvitoParam::CEILING_HEIGHT, $offer->getCeilingHeightMin()),
            new AvitoObject(AvitoParam::HEATING,  $this->dataMapper->getHeating($offer)),
            new AvitoObject(AvitoParam::BUILDING_CLASS, $this->dataMapper->getBuildingClass($offer)),
            new AvitoObject(AvitoParam::ENTRANCE_ADDITIONALLY, AvitoValue::ENTRANCE_ADDITIONALLY_SEPARATE),
            new AvitoObject(AvitoParam::POWER_GRID_CAPACITY, $offer->getPowerCapacity()),
            new AvitoObject(AvitoParam::BUILDING_TYPE, AvitoValue::BUILDING_TYPE_OTHER),
        ];
    }

    /**
     * @param OfferInterface $offer
     * @return AvitoObject[]
     */
    private function getDataForLandSale(OfferInterface $offer): array
    {
        return [
            new AvitoObject(AvitoParam::LAND_AREA, $offer->getMaxAreaPerSotka()),
        ];
    }
}