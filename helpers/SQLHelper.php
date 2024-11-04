<?php

declare(strict_types=1);

namespace app\helpers;

use yii\db\Expression;

/**
 * The SQLHelper class provides a set of static methods that help to generate SQL expressions.
 */
class SQLHelper
{
	/**
	 * Converts a Unix timestamp to a MySQL datetime format.
	 *
	 * This method generates a SQL expression that converts a Unix timestamp to a MySQL datetime format.
	 *
	 * @param ?string $time The Unix timestamp to be converted (timestamp or field name).
	 *
	 * @return string The generated SQL expression.
	 */
	public static function fromUnixTime(?string $time): string
	{
		return 'FROM_UNIXTIME(' . $time . ')';
	}

	public static function toUnixTime(?string $date): string
	{
		return 'UNIX_TIMESTAMP(' . $date . ')';
	}

	/**
	 * Subtracts a time interval from a date.
	 *
	 * This method generates a SQL expression that subtracts a specified time interval from a given date.
	 *
	 * @param string $date     The date from which the interval will be subtracted.
	 * @param string $interval The time interval to subtract (e.g., '1 DAY', '2 HOUR').
	 *
	 * @return string The generated SQL expression.
	 */
	public static function dateSub(string $date, string $interval): string
	{
		return 'DATE_SUB(' . $date . ', INTERVAL ' . $interval . ')';
	}

	/** Concatenates a list of expressions with a default value for NULL values.
	 *
	 * @param array  $values
	 * @param string $defaultValue
	 *
	 * @return string
	 */
	public static function concatWithCoalesce(array $values, string $defaultValue = ''): string
	{
		$concat = 'CONCAT(';

		foreach ($values as $value) {
			$concat .= 'COALESCE(' . $value . ', \'' . $defaultValue . '\'), ';
		}

		$concat = rtrim($concat, ', ');
		$concat .= ')';

		return $concat;
	}

	/** Generates a SQL expression that compares a field with the current date and time.
	 *
	 * @param string $field
	 *
	 * @return array
	 */
	public static function beforeNow(string $field): array
	{
		return ['<', $field, new Expression('NOW()')];
	}

	/** Generates a SQL expression that compares a field with the current date and time.
	 *
	 * @param string $field
	 *
	 * @return array
	 */
	public static function afterNow(string $field): array
	{
		return ['>', $field, new Expression('NOW()')];
	}
}