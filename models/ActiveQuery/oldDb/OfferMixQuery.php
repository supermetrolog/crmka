<?php

declare(strict_types=1);

namespace app\models\ActiveQuery\oldDb;

use app\helpers\DbHelper;
use app\models\Company;
use yii\base\ErrorException;
use yii\db\ActiveQuery;
use app\models\oldDb\OfferMix;
use yii\db\ActiveRecord;

class OfferMixQuery extends ActiveQuery
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
    public function one($db = null)
    {
        $this->limit(1);
        return parent::one($db);
    }

    /**
     * @return OfferMixQuery
     * @throws ErrorException
     */
    public function search(): OfferMixQuery
    {
        return $this->groupBy(DbHelper::getField(OfferMix::tableName(), 'id'))
            ->with(['object'])
            ->joinForSearch();
    }

    /**
     * @param bool $eagerLoading
     * @return self
     * @throws ErrorException
     */
    public function joinForSearch(bool $eagerLoading = false): self
    {
        $joinedDbName = DbHelper::getDsnAttribute('dbname', Company::getDb()->dsn);

        $phonesJoin = function ($query) use ($joinedDbName) {
            return $query->from("$joinedDbName.phone");
        };
        $contactsJoin = function ($query) use ($joinedDbName, $phonesJoin) {
            return $query->from("$joinedDbName.contact")->joinWith(['phones' => $phonesJoin]);
        };
        $companyJoin = function ($query) use ($joinedDbName, $contactsJoin) {
            return $query->from("$joinedDbName.company")->joinWith(['contacts' => $contactsJoin]);
        };

        return $this->joinWith(['company' => $companyJoin], $eagerLoading)
            ->joinWith(['block'], $eagerLoading);
    }
}
