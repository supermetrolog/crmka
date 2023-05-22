<?php

namespace app\components\interfaces;

interface OfferInterface
{
    public const OBJECT_TYPE_WAREHOUSE = 0;
    public const OBJECT_TYPE_PRODUCTION = 1;
    public const OBJECT_TYPE_LAND = 2;

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
     * @return float
     */
    function getPrice(): float;

    /**
     * @return array
     */
    function getObjectTypes(): array;
    function isRentType(): bool;
    function isSubleaseType(): bool;
    function isSaleType(): bool;
    function isLand(): bool;
    function isWarehouse(): bool;
    function isProduction(): bool;
    function hasDeposit(): bool;
}