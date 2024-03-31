<?php

declare(strict_types=1);

namespace app\helpers;

use yii\base\ErrorException;

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
	public static function getDBName(string $dsn): string
	{
		return self::getDsnAttribute('dbname', $dsn);
	}

    public static function getField(string $tableName, string $field): string
    {
        return $tableName . '.' . $field;
    }

	public static function getDBField(string $dbName, string $tableName, string $field): string
	{
		return $dbName . '.' . $tableName . '.' . $field;
	}

	public static function getTablePath(string $dbName, string $tableName): string
	{
		return $dbName . '.' . $tableName;
	}
}
