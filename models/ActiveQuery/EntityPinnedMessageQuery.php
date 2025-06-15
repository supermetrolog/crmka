<?php

declare(strict_types=1);

namespace app\models\ActiveQuery;

use app\kernel\common\models\AQ\AQ;
use app\kernel\common\models\AQ\SoftDeleteTrait;
use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\models\EntityPinnedMessage;

class EntityPinnedMessageQuery extends AQ
{
	use SoftDeleteTrait;

	/**
	 * @return EntityPinnedMessage[]
	 */
	public function all($db = null): array
	{
		return parent::all($db);
	}

	public function one($db = null): ?EntityPinnedMessage
	{
		/** @var ?EntityPinnedMessage */
		return parent::one($db);
	}

	/**
	 * @throws ModelNotFoundException
	 */
	public function oneOrThrow($db = null): EntityPinnedMessage
	{
		/** @var EntityPinnedMessage */
		return parent::oneOrThrow($db);
	}
}