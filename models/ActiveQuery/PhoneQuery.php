<?php

declare(strict_types=1);

namespace app\models\ActiveQuery;

use app\kernel\common\models\AQ\AQ;
use app\kernel\common\models\AQ\SoftDeleteTrait;
use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\models\miniModels\Phone;

class PhoneQuery extends AQ
{
	use SoftDeleteTrait;

	public function one($db = null): ?Phone
	{
		/** @var Phone */
		return parent::one($db);
	}

	/**
	 * @throws ModelNotFoundException
	 */
	public function oneOrThrow($db = null): Phone
	{
		/** @var Phone */
		return parent::oneOrThrow($db);
	}

	/**
	 * @return Phone[]
	 */
	public function all($db = null): array
	{
		return parent::all($db);
	}
}