<?php

namespace app\models\ActiveQuery;

use app\kernel\common\models\AQ\AQ;
use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\models\Timeline;

class TimelineQuery extends AQ
{
	/**
	 * @return Timeline[]
	 */
	public function all($db = null): array
	{
		return parent::all($db);
	}

	/**
	 * @return Timeline|array|null
	 */
	public function one($db = null): ?Timeline
	{
		return parent::one($db);
	}

	/**
	 * @return Timeline|array
	 * @throws ModelNotFoundException
	 */
	public function oneOrThrow($db = null): Timeline
	{
		return parent::oneOrThrow($db);
	}

	public function active(): self
	{
		return $this->andWhere([$this->field('status') => Timeline::STATUS_ACTIVE]);
	}
}
