<?php

namespace app\models\ActiveQuery;

use app\kernel\common\models\AQ\AQ;
use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\models\Complex;

class ComplexQuery extends AQ
{
	/**
	 * @return Complex[]
	 */
	public function all($db = null): array
	{
		return parent::all($db);
	}

	/**
	 * @throws ModelNotFoundException
	 */
	public function oneOrThrow($db = null): Complex
	{
		/** @var Complex */
		return parent::oneOrThrow($db);
	}

	public function one($db = null): ?Complex
	{
		$this->limit(1);

		/** @var Complex */
		return parent::one($db);
	}

	public function byId(int $id): self
	{
		return $this->andWhere([Complex::tableName() . '.id' => $id]);
	}
}