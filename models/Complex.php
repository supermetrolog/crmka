<?php

namespace app\models;

use app\models\ActiveQuery\ComplexQuery;
use yii\helpers\Json;

class Complex extends oldDb\Complex
{

    /**
     * @return array
     */
    public function getPhotos(): array
    {
        return Json::decode($this->photo);
    }

    /**
     * @return array
     */
    public function fields(): array
    {
        $fields = parent::fields();
        unset($fields['photo']);
        $fields['photos'] = function () { return $this->getPhotos(); };
        return $fields;
    }

    /**
     * @return ComplexQuery
     */
    public static function find(): ComplexQuery
    {
        return new ComplexQuery(get_called_class());
    }
}