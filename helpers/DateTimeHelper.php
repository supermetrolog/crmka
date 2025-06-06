<?php

declare(strict_types=1);

namespace app\helpers;

use DateTime;
use DateTimeInterface;
use Exception;
use Throwable;

class DateTimeHelper
{
	public static function isValid(string $datetime): bool
	{
		try {
			self::make($datetime);

			return true;
		} catch (Throwable $e) {
			return false;
		}
	}

	public static function now(): DateTimeInterface
	{
		return new DateTime();
	}

	public static function nowf(string $format = 'Y-m-d H:i:s'): string
	{
		return (new DateTime())->format($format);
	}

	/**
	 * @throws Exception
	 */
	public static function tryMake(?string $datetime): ?DateTimeInterface
	{
		if (!$datetime) {
			return null;
		}

		return self::make($datetime);
	}

	/**
	 * @throws Exception
	 */
	public static function tryMakef(?string $datetime, string $format = 'Y-m-d H:i:s'): ?string
	{
		if (!$datetime) {
			return null;
		}

		return self::makef($datetime, $format);
	}

	/**
	 * @throws Exception
	 */
	public static function make(string $datetime): DateTimeInterface
	{
		return new DateTime($datetime);
	}

	/**
	 * @throws Exception
	 */
	public static function makef(string $datetime, string $format = 'Y-m-d H:i:s'): string
	{
		return self::make($datetime)->format($format);
	}

	public static function fromUnix(int $timestamp): DateTimeInterface
	{
		return (new DateTime())->setTimestamp($timestamp);
	}

	public static function fromUnixf(int $timestamp, string $format = 'Y-m-d H:i:s'): string
	{
		return (new DateTime())->setTimestamp($timestamp)->format($format);
	}

	public static function unix(): int
	{
		return time();
	}

	/**
	 * @throws Exception
	 */
	public static function makeUnix(string $datetime): int
	{
		return self::make($datetime)->getTimestamp();
	}

	public static function isSameDate(DateTimeInterface $first, DateTimeInterface $second): bool
	{
		return $first->format('Y-m-d') === $second->format('Y-m-d');
	}

	public static function diffInMinutes(DateTimeInterface $first, DateTimeInterface $second, bool $absolute = true): int
	{
		$diff = (int)(($first->getTimestamp() - $second->getTimestamp()) / 60);

		return $absolute ? abs($diff) : $diff;
	}

	public static function format(DateTimeInterface $dateTime, string $format = 'Y-m-d H:i:s'): string
	{
		return $dateTime->format($format);
	}

	public static function tryFormat(?DateTimeInterface $dateTime, string $format = 'Y-m-d H:i:s', ?string $fallback = null): ?string
	{
		if (is_null($dateTime)) {
			return $fallback;
		}

		return self::format($dateTime, $format);
	}

	public static function getDayEndTime(DateTimeInterface $dateTime): DateTimeInterface
	{
		return (clone $dateTime)->setTime(23, 59, 59);
	}

	public static function getDayStartTime(DateTimeInterface $dateTime): DateTimeInterface
	{
		return (clone $dateTime)->setTime(0, 0, 0);
	}
}