<?php

namespace app\kernel\common\models\AQ;

use app\kernel\common\models\AR\AR;
use app\kernel\common\models\exceptions\ModelNotFoundException;

/**
 * @template-covariant Model of AR
 */
class ModelAQ extends AQ
{
	use SoftDeleteTrait;

	// AQ Methods

	/**
	 * @return Model[]
	 */
	public function all($db = null): array
	{
		/** @var Model[] */
		return parent::all($db);
	}

	/**
	 * @return ?Model
	 */
	public function one($db = null): ?AR
	{
		/** @var ?Model */
		return parent::one($db);
	}

	/**
	 * @return Model
	 *
	 * @throws ModelNotFoundException
	 */
	public function oneOrThrow($db = null): AR
	{
		/** @var Model */
		return parent::oneOrThrow($db);
	}
}