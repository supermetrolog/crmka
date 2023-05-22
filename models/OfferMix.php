<?php

namespace app\models;

use app\components\interfaces\OfferInterface;
use app\models\ActiveQuery\OfferMixQuery;
use yii\base\ErrorException;
use yii\helpers\Json;

class OfferMix extends oldDb\OfferMix implements OfferInterface
{
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
    public function isSubleaseType(): bool
    {
        return $this->deal_type === self::DEAL_TYPE_SUBLEASE;
    }

    /**
     * @return float
     * @throws ErrorException
     */
    public function getPrice(): float
    {
        if ($this->isRentType() || $this->isSubleaseType()) {
            return 1;
        }

        if ($this->isSaleType()) {
            return 2;
        }

        throw new ErrorException('Unknown offer type');
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
    function getID(): int
    {
        return $this->original_id;
    }

    /**
     * @return string
     */
    function getDescription(): string
    {
        return $this->description;
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
        return Json::decode($this->object_type);
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
}