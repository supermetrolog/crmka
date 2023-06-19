<?php

namespace app\models\ActiveQuery;

use app\models\Complex;
use yii\db\ActiveQuery;

class ComplexQuery extends ActiveQuery
{
    /**
     * @param $db
     * @return array|Complex[]
     */
    public function all($db = null): array
    {
        return parent::all($db);
    }

    /**
     * @param $db
     * @return array|Complex|null
     */
    public function one($db = null)
    {
        $this->limit(1);
        return parent::one($db);
    }

    /**
     * @param int $id
     * @return self
     */
    public function byId(int $id): self
    {
        return $this->andWhere(['id' => $id]);
    }
}