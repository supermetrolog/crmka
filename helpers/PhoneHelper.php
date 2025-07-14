<?php

declare(strict_types=1);

namespace app\helpers;

use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumber;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;

class PhoneHelper
{
	public const FORMAT_INTERNATIONAL = PhoneNumberFormat::INTERNATIONAL;
	public const FORMAT_NATIONAL      = PhoneNumberFormat::NATIONAL;
	public const FORMAT_E164          = PhoneNumberFormat::E164;
	public const FORMAT_RFC3966       = PhoneNumberFormat::RFC3966;

	private static function getUtil(): PhoneNumberUtil
	{
		return PhoneNumberUtil::getInstance();
	}

	public static function isPossibleNumber(string $number): bool
	{
		return self::getUtil()->isPossibleNumber($number);
	}

	/**
	 * @throws NumberParseException
	 */
	public static function parse(string $number, ?string $region = null): PhoneNumber
	{
		return self::getUtil()->parse($number, $region);
	}

	public static function isValidNumber(PhoneNumber $number): bool
	{

		return self::getUtil()->isValidNumber($number);
	}

	public static function isValidNumberForRegion(PhoneNumber $number, $regionCode): bool
	{
		return self::getUtil()->isValidNumberForRegion($number, $regionCode);
	}

	public static function formatNumber(PhoneNumber $number, int $format = PhoneNumberFormat::NATIONAL): string
	{
		return self::getUtil()->format($number, $format);
	}

	public static function tryFormat(string $number, int $format = PhoneNumberFormat::NATIONAL, string $countryCode = null): string
	{
		try {
			$phone = self::parse($number, $countryCode);

			return self::getUtil()->format($phone, $format);
		} catch (NumberParseException $e) {
			return $number;
		}
	}
}