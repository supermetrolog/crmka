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
     * @param AvitoObject[] $data
     * @return array
     */
    private function clean(array $data): array
    {
        $cleanedData = [];
        foreach ($data as $item) {
            if (!is_null($item->value)) {
                $cleanedData[] = $item;
            }
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
            new AvitoObject(AvitoParam::ID, $offer->getID()),
            new AvitoObject(AvitoParam::DESCRIPTION, $offer->getDescription()),
            new AvitoObject(AvitoParam::ADDRESS, $offer->getAddress()),
            new AvitoObject(AvitoParam::LATITUDE, $offer->getLatitude()),
            new AvitoObject(AvitoParam::LONGITUDE, $offer->getLongitude()),
            new AvitoObject(AvitoParam::CATEGORY,  $this->dataMapper->getCategory($offer)),
            new AvitoObject(AvitoParam::PRICE, $offer->getPrice()), // TODO: fix
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
            new AvitoObject(AvitoParam::OPERATION_TYPE, AvitoValue::OPERATION_TYPE_RENT),
            new AvitoObject(AvitoParam::RENTAL_TYPE, $this->dataMapper->getRentalType($offer)),
            new AvitoObject(AvitoParam::LEASE_DEPOSIT,  $this->dataMapper->getLeaseDeposit($offer)),
            new AvitoObject(AvitoParam::LEASE_COMMISSION_SIZE, 0),
            new AvitoObject(AvitoParam::ENTRANCE, AvitoValue::ENTRANCE_FROM_STREET),
            new AvitoObject(AvitoParam::FLOOR, $offer->getFloorMin()),
            new AvitoObject(AvitoParam::FLOOR_ADDITIONALLY, $this->dataMapper->getFloorAdditionally($offer)),
            new AvitoObject(AvitoParam::PARKING_TYPE, AvitoValue::PARKING_TYPE_IN_THE_STREET),
            new AvitoObject(AvitoParam::SQUARE, 2000), // TODO: fix
            new AvitoObject(AvitoParam::SQUARE_ADDITIONALLY, AvitoValue::SQUARE_ADDITIONAL_POSSIBLE_CUTTING), // TODO: fix
            new AvitoObject(AvitoParam::PRICE_TYPE, AvitoValue::PRICE_TYPE_PER_MONTH_PER_SQUARE_METER), // TODO: fix
            new AvitoObject(AvitoParam::CEILING_HEIGHT, $offer->getCeilingHeightMin()),
            new AvitoObject(AvitoParam::HEATING,  $this->dataMapper->getHeating($offer)),
            new AvitoObject(AvitoParam::BUILDING_CLASS, $this->dataMapper->getBuildingClass($offer)),
            new AvitoObject(AvitoParam::LEASE_PRICE_OPTIONS, $this->dataMapper->getLeasePriceOptions($offer)),
            new AvitoObject(AvitoParam::ENTRANCE_ADDITIONALLY, AvitoValue::ENTRANCE_ADDITIONALLY_SEPARATE),
        ];
    }

    /**
     * @param OfferInterface $offer
     * @return AvitoObject[]
     */
    private function getDataForLandRent(OfferInterface $offer): array
    {
        return [
            new AvitoObject(AvitoParam::LAND_AREA, 1), // TODO: fix
            new AvitoObject(AvitoParam::LEASE_COMMISSION_SIZE, 0), // TODO: fix
            new AvitoObject(AvitoParam::LEASE_DEPOSIT,  $this->dataMapper->getLeaseDeposit($offer)), // TODO: fix
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
            new AvitoObject(AvitoParam::OPERATION_TYPE, AvitoValue::OPERATION_TYPE_SALE),
            new AvitoObject(AvitoParam::TRANSACTION_TYPE, AvitoValue::TRANSACTION_TYPE_SALE),
            new AvitoObject(AvitoParam::ENTRANCE, AvitoValue::ENTRANCE_FROM_STREET),
            new AvitoObject(AvitoParam::FLOOR, 2), // TODO: fix
            new AvitoObject(AvitoParam::FLOOR_ADDITIONALLY, [[
                'tag' => 'Option',
                'value' => AvitoValue::SEVERAL_FLOORS
            ]]), // TODO: fix
            new AvitoObject(AvitoParam::PARKING_TYPE, AvitoValue::PARKING_TYPE_IN_THE_STREET),
            new AvitoObject(AvitoParam::SQUARE, 2000), // TODO: fix
            new AvitoObject(AvitoParam::SQUARE_ADDITIONALLY,AvitoValue::SQUARE_ADDITIONAL_POSSIBLE_CUTTING), // TODO: fix
            new AvitoObject(AvitoParam::CEILING_HEIGHT, $offer->getCeilingHeightMin()), // TODO: fix
            new AvitoObject(AvitoParam::HEATING, AvitoValue::HEATING_HAS_NOT), // TODO: fix
            new AvitoObject(AvitoParam::BUILDING_CLASS, AvitoValue::BUILDING_CLASS_A), // TODO: fix
            new AvitoObject(AvitoParam::ENTRANCE_ADDITIONALLY, AvitoValue::ENTRANCE_ADDITIONALLY_SEPARATE), // TODO: fix
            new AvitoObject(AvitoParam::POWER_GRID_CAPACITY, $offer->getPowerCapacity()), // TODO: fix
            new AvitoObject(AvitoParam::POWER_GRID_ADDITIONALLY, ['option' => AvitoValue::POWER_GRID_ADDITIONALLY_POSSIBLE_INCREASE]), // TODO: fix
        ];
    }

    /**
     * @param OfferInterface $offer
     * @return AvitoObject[]
     */
    private function getDataForLandSale(OfferInterface $offer): array
    {
        return [];
    }
}