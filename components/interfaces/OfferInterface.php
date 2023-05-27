<?php

namespace app\components\interfaces;

interface OfferInterface
{
    public const OBJECT_TYPE_WAREHOUSE = 0;
    public const OBJECT_TYPE_PRODUCTION = 1;
    public const OBJECT_TYPE_LAND = 2;

    public const HEATING_AUTO = 1;
    public const HEATING_CENTRAL = 2;

    /**
     * @return string
     */
    function getVisibleID(): string;

    /**
     * @return int
     */
    function getID(): int;

    /**
     * @return string
     */
    function getDescription(): string;

    /**
     * @return string
     */
    function getAddress(): string;

    /**
     * @return string
     */
    function getLatitude(): string;

    /**
     * @return string
     */
    function getLongitude(): string;

    /**
     * @return array
     */
    function getObjectTypes(): array;

    /**
     * @return bool
     */
    function isRentType(): bool;

    /**
     * @return bool
     */
    function isSubleaseType(): bool;

    /**
     * @return bool
     */
    function isSaleType(): bool;

    /**
     * @return bool
     */
    function isLand(): bool;

    /**
     * @return bool
     */
    function isWarehouse(): bool;

    /**
     * @return bool
     */
    function isProduction(): bool;

    /**
     * @return bool
     */
    function hasDeposit(): bool;

    /**
     * @return string
     */
    function getFullConsultantName(): string;

    /**
     * @return string
     */
    function getContactPhone(): string;

    /**
     * @return string[]
     */
    function getImages(): array;

    /**
     * @return float
     */
    function getCeilingHeightMin(): float;

    /**
     * @return float
     */
    function getPowerCapacity(): float;

    /**
     * @return bool
     */
    function hasRentalHolidays(): bool;

    /**
     * @return float
     */
    function getDepositMonth(): float;

    /**
     * @return int
     */
    function getFloorMin(): int;

    /**
     * @return bool
     */
    function hasSeveralFloors(): bool;
    function hasHeating(): bool;
    function getHeatingType(): int;
    function getClass(): string;
    function isIncludeOPEX(): bool;
    function isIncludePublicService(): bool;
    function getMaxPrice(): float;
}