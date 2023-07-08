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

    /**
     * @return ActiveQuery
     */
    public function getCompanyRecord(): ActiveQuery
    {
        return $this->hasOne(Company::class, ['id' => 'company_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getBlocks(): ActiveQuery
    {
        return $this->hasMany(Block::class, ['offer_id' => 'id']);
    }
}
