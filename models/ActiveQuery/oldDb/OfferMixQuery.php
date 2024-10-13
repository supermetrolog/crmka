<?php

declare(strict_types=1);

namespace app\models\ActiveQuery\oldDb;

use app\helpers\DbHelper;
use app\kernel\common\models\AQ\AQ;
use app\models\Call;
use app\models\ChatMember;
use app\models\Company;
use app\models\ObjectChatMember;
use app\models\oldDb\OfferMix;
use app\models\Relation;
use yii\base\ErrorException;
use yii\db\ActiveRecord;

class OfferMixQuery extends AQ
{
	/**
	 * @param mixed $db
	 *
	 * @return OfferMix[]
	 */
	public function all($db = null): array
	{
		return parent::all($db);
	}

	/**
	 * @param mixed $db
	 *
	 * @return OfferMix|null|ActiveRecord
	 */
	public function one($db = null): ?OfferMix
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
	 * @throws ErrorException
	 */

	public function leftJoinLastCallRelation(): self
	{
		$maxIdsSubQuery = Relation::find()
		                          ->from(Relation::getTable())
		                          ->select(['MAX(id)'])
		                          ->byFirstType(ChatMember::getMorphClass())
		                          ->bySecondType(Call::getMorphClass())
		                          ->groupBy(['first_id', 'first_type']);

		$subQuery = Relation::find()
		                    ->select([Relation::field('*'), 'object_id' => 'ocm.object_id'])
		                    ->from(Relation::getTable())
		                    ->leftJoin([ChatMember::getTable()], ChatMember::xfield('id') . '=' . Relation::xfield('first_id'))
		                    ->leftJoin(['ocm' => ObjectChatMember::getTable()], 'ocm.id =' . ChatMember::xfield('model_id'))
		                    ->byFirstType(ChatMember::getMorphClass())
		                    ->bySecondType(Call::getMorphClass())
		                    ->andWhere([Relation::field('id') => $maxIdsSubQuery]);

		$this->leftJoin(['last_call_rel' => $subQuery], $this->field('object_id') . '=' . 'last_call_rel.object_id');

		return $this;
	}


	/**
	 * @param bool $eagerLoading
	 *
	 * @return self
	 * @throws ErrorException
	 */
	public function joinForSearch(bool $eagerLoading = false): self
	{
		$joinedDbName = DbHelper::getDsnAttribute('dbname', Company::getDb()->dsn);

		$phonesJoin   = function ($query) use ($joinedDbName) {
			return $query->from("$joinedDbName.phone");
		};
		$contactsJoin = function ($query) use ($joinedDbName, $phonesJoin) {
			return $query->from("$joinedDbName.contact")->joinWith(['phones' => $phonesJoin]);
		};
		$companyJoin  = function ($query) use ($joinedDbName, $contactsJoin) {
			return $query->from("$joinedDbName.company")->joinWith(['contacts' => $contactsJoin]);
		};

		return $this->joinWith(['company' => $companyJoin], $eagerLoading)
		            ->joinWith(['block'], $eagerLoading);
	}

	/**
	 * @throws ErrorException
	 */
	public function notDeleted(): self
	{
		return $this->andWhere([OfferMix::field('deleted') => 0]);
	}
}
