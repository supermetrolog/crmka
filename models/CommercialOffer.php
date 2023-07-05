<?php

namespace app\models;

use app\models\oldDb\Offers;
use yii\db\ActiveQuery;

class CommercialOffer extends Offers
{
    /**
     * @return ActiveQuery
     */
    public function getDealTypeRecord(): ActiveQuery
    {
        return $this->hasOne(DealType::class, ['id' => 'deal_type']);
    }
}
