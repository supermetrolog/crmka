<?php

namespace app\helpers;

use yii\helpers\Json;

class JsonFieldNormalizer
{
    /**
     * @param mixed $value
     * @return array
     */
    public static function jsonToArrayWithIntElements($value): array
    {
        if (!is_string($value)) {
            return [];
        }

        $decoded = Json::decode($value);

        if (!is_array($decoded)) {
            return [];
        }

        $mapCallback = function ($elem) {
            if ($elem || is_int($elem)) {
                return (int)$elem;
            }

            return null;
        };

        $filterCallback = function ($elem){
            return !is_null($elem);
        };

        return array_filter(array_map($mapCallback, $decoded), $filterCallback);
    }
}