<?php

namespace app\models;

use app\helpers\JsonFieldNormalizer;
use app\models\ActiveQuery\ComplexQuery;
use app\models\location\Location;
use app\models\oldDb\User as OldDbUser;
use yii\db\ActiveQuery;
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
    public function getInternetType(): array
    {
        return JsonFieldNormalizer::jsonToArrayWithIntElements($this->internet_type);
    }

    /**
     * @return array
     */
    public function getGuardType(): array
    {
        return JsonFieldNormalizer::jsonToArrayWithIntElements($this->guard_type);
    }

    /**
     * @return array
     */
    public function getCranesGantry(): array
    {
        return JsonFieldNormalizer::jsonToArrayWithIntElements($this->cranes_gantry);
    }

    /**
     * @return array
     */
    public function getCranesRailway(): array
    {
        return JsonFieldNormalizer::jsonToArrayWithIntElements($this->cranes_railway);
    }



    /**
     * @return array
     */
    public function fields(): array
    {
        $fields = parent::fields();
        unset($fields['photo']);
        $fields['photos'] = function () { return $this->getPhotos(); };
        $fields['internet_type'] = function () { return $this->getInternetType(); };
        $fields['guard_type'] = function () { return $this->getGuardType(); };
        $fields['cranes_gantry'] = function () { return $this->getCranesGantry(); };
        $fields['cranes_railway'] = function () { return $this->getCranesRailway(); };
        return $fields;
    }

    /**
     * @return ComplexQuery
     */
    public static function find(): ComplexQuery
    {
        return new ComplexQuery(get_called_class());
    }

    /**
     * @return ActiveQuery
     */
    public function getObjects(): ActiveQuery
    {
        return $this->hasMany(Objects::class, ['complex_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getLocation(): ActiveQuery
    {
        return $this->hasOne(Location::class, ['id' => 'location_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getOldUser(): ActiveQuery
    {
        return $this->hasOne(OldDbUser::class, ['id' => 'author_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getAuthor(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'user_id_new'])->via('oldUser');
    }

    /**
     * @return ActiveQuery
     */
    public function getAgent(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'user_id_new'])->via('oldUser');
    }
}