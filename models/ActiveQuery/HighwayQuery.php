<?php

namespace app\models\ActiveQuery;

use app\models\location\Highway;
use yii\db\ActiveQuery;

class HighwayQuery extends ActiveQuery
{
    /**
     * @param $db
     * @return array|null|Highway
     */
    public function one($db = null)
    {
        $this->limit(1);
        return parent::one($db);
    }

    /**
     * @param $db
     * @return array|Highway[]
     */
    public function all($db = null): array
    {
        return parent::all($db);
    }

    /**
     * @param array $ids
     * @return self
     */
    public function byIds(array $ids): self
    {
        return $this->andWhere(['id' => $ids]);
    }
}