<?php

declare(strict_types=1);

namespace app\helpers;

use yii\base\ErrorException;
use yii\db\ActiveRecord;
use yii\db\Connection;

class DbHelper
{
	/**
	 * @throws ErrorException
	 */
	public static function getDsnAttribute(string $name, string $dsn): string
	{
		if (preg_match('/' . $name . '=([^;]*)/', $dsn, $match)) {
			return $match[1];
		}

		throw new ErrorException('invalid dsn');
	}

	/**
	 * @throws ErrorException
	 */
	public static function getDbName(Connection $connection): string
	{
		return self::getDsnAttribute('dbname', $connection->dsn);
	}

	public static function getField(string $tableName, string $field): string
	{
		return self::getPath($tableName, $field);
	}

	public static function getDBField(string $dbName, string $tableName, string $field): string
	{
		return self::getPath($dbName, $tableName, $field);
	}

	public static function getTablePath(string $dbName, string $tableName): string
	{
		return self::getPath($dbName, $tableName);
	}

	/**
	 * @param string|ActiveRecord $modelClass
	 * @param string              $field
	 *
	 * @return string
	 * @throws ErrorException
	 */
	public static function getFullPathAR(string $modelClass, string $field): string
	{
		return self::getDBField(self::getDbName($modelClass::getDb()), $modelClass::tableName(), $field);
	}

	public static function getPath(string $path, string ...$parts): string
	{
		return implode('.', [$path, ...$parts]);
	}
}
