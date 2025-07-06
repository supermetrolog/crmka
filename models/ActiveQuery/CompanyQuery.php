<?php

declare(strict_types=1);

namespace app\models\ActiveQuery;

use app\kernel\common\models\AQ\AQ;
use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\models\Call;
use app\models\ChatMember;
use app\models\Company;
use app\models\Relation;
use yii\base\ErrorException;

class CompanyQuery extends AQ
{
	/**
	 * @return Company[]
	 */
	public function all($db = null): array
	{
		return parent::all($db);
	}

	public function one($db = null): ?Company
	{
		/** @var ?Company */
		return parent::one($db);
	}

	/**
	 * @throws ModelNotFoundException
	 */
	public function oneOrThrow($db = null): Company
	{
		/** @var Company */
		return parent::oneOrThrow($db);
	}

	/**
	 * @throws ErrorException
	 */
	public function leftJoinLastCallRelation(string $chatMemberTableAlias = 'cm'): self
	{
		$maxIdsSubQuery = Relation::find()
		                          ->from(Relation::getTable())
		                          ->select(['MAX(id)'])
		                          ->byFirstType(ChatMember::getMorphClass())
		                          ->bySecondType(Call::getMorphClass())
		                          ->groupBy(['first_id', 'first_type']);

		$subQuery = Relation::find()
		                    ->from(Relation::getTable())
		                    ->byFirstType(ChatMember::getMorphClass())
		                    ->bySecondType(Call::getMorphClass())
		                    ->andWhere([Relation::field('id') => $maxIdsSubQuery]);

		$this->leftJoin(['last_call_rel' => $subQuery], "$chatMemberTableAlias.id" . '=' . 'last_call_rel.first_id');

		return $this;
	}

	/**
	 * @throws ErrorException
	 */
	public function byStatus(int $status): self
	{
		return $this->andWhere([Company::field('status') => $status]);
	}

	/**
	 * @throws ErrorException
	 */
	public function active(): self
	{
		return $this->byStatus(Company::STATUS_ACTIVE);
	}

	/**
	 * @throws ErrorException
	 */
	public function createdAfter(string $date): self
	{
		return $this->andWhere(['>', Company::field('created_at'), $date]);
	}
}