<?php

namespace app\components\interfaces;

interface OfferInterface
{
	public const OBJECT_TYPE_WAREHOUSE  = 1;
	public const OBJECT_TYPE_PRODUCTION = 2;
	public const OBJECT_TYPE_LAND       = 3;

	public const HEATING_AUTO    = 1;
	public const HEATING_CENTRAL = 2;

	/**
	 * @return string
	 */
	function getVisibleID(): string;

	/**
	 * @return string
	 */
	function getVisualId(): string;

	/**
	 * @return string
	 */
	function getUniqueId(): string;

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
	function isResponseStorageType(): bool;

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

	function getCeilingHeightMin(): float;
	
	function getCeilingHeightMax(): float;

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

	function getFloorMax(): int;

	/**
	 * @return bool
	 */
	function hasSeveralFloors(): bool;

	/**
	 * @return bool
	 */
	function hasHeating(): bool;

	/**
	 * @return int
	 */
	function getHeatingType(): int;

	/**
	 * @return string
	 */
	function getClass(): string;

	/**
	 * @return bool
	 */
	function isIncludeOPEX(): bool;

	/**
	 * @return bool
	 */
	function isIncludePublicService(): bool;

	/**
	 * @return float
	 */
	function getMaxPrice(): float;

	/**
	 * @return float
	 */
	function getMaxArea(): float;

	/**
	 * @return float
	 */
	function getMaxAreaPerSotka(): float;

	/**
	 * @return bool
	 */
	function isSolid(): bool;

	/**
	 * @return string
	 */
	function getAvitoAdStartDate(): string;
}