<?php

namespace app\models;

use app\models\ActiveQuery\OfferMixQuery;

class OfferMix extends oldDb\OfferMix
{

    /**
     * @return OfferMixQuery
     */
    public static function find(): OfferMixQuery
    {
        return new OfferMixQuery(get_called_class());
    }
}