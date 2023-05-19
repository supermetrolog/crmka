<?php

declare(strict_types=1);

namespace app\models\ActiveQuery;

use app\models\OfferMix;
use yii\db\ActiveRecord;

class OfferMixQuery extends oldDb\OfferMixQuery
{
    /**
     * @param  mixed $db
     * @return OfferMix[]
     */
    public function all($db = null): array
    {
        return parent::all($db);
    }
    /**
     * @param  mixed $db
     * @return OfferMix|null|ActiveRecord
     */
    public function one($db = null): ?OfferMix
    {
        $this->limit(1);
        return parent::one($db);
    }


    /**
     * @return self
     */
    public function notDelete(): self
    {
        return $this->andWhere(['!=', 'deleted', 1]);
    }

    /**
     * @return self
     */
    public function active(): self
    {
        return $this->andWhere(['status' => 1]);
    }

    /**
     * @return self
     */
    public function offersType(): self
    {
        return $this->andWhere(['type_id' => [1, 2]]);
    }
}