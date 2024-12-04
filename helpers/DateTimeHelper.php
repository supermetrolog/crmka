<?php

declare(strict_types=1);

namespace app\helpers;

use DateTime;
use DateTimeInterface;
use Exception;

class DateTimeHelper
{
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
}