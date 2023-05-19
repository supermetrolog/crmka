<?php

declare(strict_types=1);

namespace app\helpers;

use yii\base\ErrorException;

class DbHelper
{
    /**
     * @param string $name
     * @param string $dsn
     * @return string
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
     * @param string $tableName
     * @param string $field
     * @return string
     */
    public static function getField(string $tableName, string $field): string
    {
        return $tableName . '.' . $field;
    }
}
