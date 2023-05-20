<?php

namespace app\models;

use app\models\ActiveQuery\OfferMixQuery;

class OfferMix extends oldDb\OfferMix
{
    /**
     * @return bool
     */
    public function isSaleType(): bool
    {
        return $this->deal_type === self::DEAL_TYPE_SALE;
    }

    /**
     * @return bool
     */
    public function isRentType(): bool
    {
        return $this->deal_type === self::DEAL_TYPE_RENT;
    }

    /**
     * @return bool
     */
    public function isSubleaseType(): bool
    {
        return $this->deal_type === self::DEAL_TYPE_SUBLEASE;
    }

    /**
     * @return OfferMixQuery
     */
    public static function find(): OfferMixQuery
    {
        return new OfferMixQuery(get_called_class());
    }
}