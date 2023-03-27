<?php

declare(strict_types=1);

namespace app\helpers;

class DbHelper
{
    /**
     * @param  string $name
     * @param  string $dsn
     * @return string
     */
    public static function getDsnAttribute(string $name, string $dsn): string
    {
        if (preg_match('/' . $name . '=([^;]*)/', $dsn, $match)) {
            return $match[1];
        } else {
            return null;
        }
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
