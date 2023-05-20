<?php

namespace app\components\avito;

use app\models\OfferMix;

class AvitoConnector
{
    /** @var OfferMix[]  */
    private array $data;

    /**
     * @param OfferMix[] $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * @return array<AvitoObject[]>
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

        return $data;
    }

    /**
     * @param OfferMix $offer
     * @return array
     */
    private function getGeneralData(OfferMix $offer): array
    {
        return [
            new AvitoObject(AvitoParam::ID, $offer->visual_id),
            new AvitoObject(AvitoParam::DESCRIPTION, $offer->description), // TODO: get auto desc
            new AvitoObject(AvitoParam::ADDRESS, $offer->address),
            new AvitoObject(AvitoParam::LATITUDE, $offer->latitude),
            new AvitoObject(AvitoParam::LONGITUDE, $offer->longitude),
            new AvitoObject(AvitoParam::CATEGORY, 'Коммерческая недвижимость'), // TODO: fix
            new AvitoObject(AvitoParam::PRICE, 123), // TODO: calculate price
            new AvitoObject(AvitoParam::OBJECT_TYPE, 'Складское помещение'), // TODO: fix
            new AvitoObject(AvitoParam::PROPERTY_RIGHTS, 'Посредник'), // TODO: fix
            new AvitoObject(AvitoParam::ENTRANCE, 'С улицы'), // TODO: fix
            new AvitoObject(AvitoParam::FLOOR, 2), // TODO: fix
            new AvitoObject(AvitoParam::LAYOUT, 'Открытая'), // TODO: fix
            new AvitoObject(AvitoParam::SQUARE, 2000), // TODO: fix
            new AvitoObject(AvitoParam::PARKING_TYPE, 'На улице'), // TODO: fix
        ];
    }

    /**
     * @param OfferMix $offer
     * @return array
     */
    private function getDataForRent(OfferMix $offer): array
    {
        return [
            new AvitoObject(AvitoParam::OPERATION_TYPE, 'Сдам'), // TODO: fix
            new AvitoObject(AvitoParam::RENTAL_TYPE, 'Прямая'), // TODO: fix
            new AvitoObject(AvitoParam::LEASE_DEPOSIT, 'Без залога'), // TODO: fix
            new AvitoObject(AvitoParam::LEASE_COMMISSION_SIZE, 12), // TODO: fix
        ];
    }

    /**
     * @param OfferMix $offer
     * @return array
     */
    private function getDataForSale(OfferMix  $offer): array
    {
        return [
            new AvitoObject(AvitoParam::OPERATION_TYPE, 'Продам'), // TODO: fix
            new AvitoObject(AvitoParam::TRANSACTION_TYPE, 'Продажа'), // TODO: fix

        ];
    }
}