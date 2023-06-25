<?php

namespace app\models\location;

use app\models\ActiveQuery\HighwayQuery;
use app\models\oldDb;

class Highway extends oldDb\location\Highways
{
    /**
     * @return HighwayQuery
     */
    public static function find(): HighwayQuery
    {
        return new HighwayQuery(get_called_class());
    }
}