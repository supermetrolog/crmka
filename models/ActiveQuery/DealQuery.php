<?php

namespace app\models\ActiveQuery;

use app\models\Deal;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

class DealQuery extends ActiveQuery
{

    /**
     * @param $db
     * @return array|Deal[]
     */
    public function all($db = null): array
    {
        return parent::all($db);
    }

    /**
     * @param $db
     * @return array|ActiveRecord|null|Deal
     */
    public function one($db = null): ?Deal
    {
        return parent::one($db);
    }
}