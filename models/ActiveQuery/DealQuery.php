<?php

namespace app\models\ActiveQuery;

use app\kernel\common\models\AQ\AQ;
use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\models\Deal;

class DealQuery extends AQ
{
	/**
	 * @return Deal[]
	 */
	public function all($db = null): array
	{
		return parent::all($db);
	}

	public function one($db = null): ?Deal
	{
		/** @var Deal */
		return parent::one($db);
	}

	/**
	 * @throws ModelNotFoundException
	 */
	public function oneOrThrow($db = null): Deal
	{
		/** @var Deal */
		return parent::oneOrThrow($db);
	}
}