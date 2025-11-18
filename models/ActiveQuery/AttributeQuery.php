<?php

namespace app\models\ActiveQuery;

use app\kernel\common\models\AQ\AQ;
use app\kernel\common\models\AQ\SoftDeleteTrait;
use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\models\Attribute;

class AttributeQuery extends AQ
{
	use SoftDeleteTrait;

	/**
	 * @return Attribute[]
	 */
	public function all($db = null): array
	{
		return parent::all($db);
	}

	public function one($db = null): ?Attribute
	{
		/** @var Attribute */
		return parent::one($db);
	}

	/**
	 * @throws ModelNotFoundException
	 */
	public function oneOrThrow($db = null): Attribute
	{
		/** @var Attribute */
		return parent::oneOrThrow($db);
	}

	public function byLabel(string $label): self
	{
		return $this->andWhere([$this->field('label') => $label]);
	}

	public function byKind(string $kind): self
	{
		return $this->andWhere([$this->field('kind') => $kind]);
	}
}