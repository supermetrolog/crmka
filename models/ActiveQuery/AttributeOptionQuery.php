<?php

namespace app\models\ActiveQuery;

use app\kernel\common\models\AQ\AQ;
use app\kernel\common\models\AQ\SoftDeleteTrait;
use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\models\AttributeOption;

class AttributeOptionQuery extends AQ
{
	use SoftDeleteTrait;

	/**
	 * @return AttributeOption[]
	 */
	public function all($db = null): array
	{
		return parent::all($db);
	}

	public function one($db = null): ?AttributeOption
	{
		/** @var AttributeOption */
		return parent::one($db);
	}

	/**
	 * @throws ModelNotFoundException
	 */
	public function oneOrThrow($db = null): AttributeOption
	{
		/** @var AttributeOption */
		return parent::oneOrThrow($db);
	}
}