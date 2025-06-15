<?php

declare(strict_types=1);

namespace app\models\ActiveQuery;

use app\kernel\common\models\AQ\AQ;
use app\kernel\common\models\AQ\SoftDeleteTrait;
use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\models\CompanyPinnedMessage;

class CompanyPinnedMessageQuery extends AQ
{
	use SoftDeleteTrait;

	/**
	 * @return CompanyPinnedMessage[]
	 */
	public function all($db = null): array
	{
		return parent::all($db);
	}

	public function one($db = null): ?CompanyPinnedMessage
	{
		/** @var ?CompanyPinnedMessage */
		return parent::one($db);
	}

	/**
	 * @throws ModelNotFoundException
	 */
	public function oneOrThrow($db = null): CompanyPinnedMessage
	{
		/** @var CompanyPinnedMessage */
		return parent::oneOrThrow($db);
	}
}