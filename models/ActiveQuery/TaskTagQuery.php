<?php

namespace app\models\ActiveQuery;

use app\kernel\common\models\AQ\AQ;
use app\kernel\common\models\AQ\SoftDeleteTrait;
use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\models\TaskTag;
use yii\db\ActiveRecord;

class TaskTagQuery extends AQ
{
	use SoftDeleteTrait;

	/**
	 * @return TaskTag[]|ActiveRecord[]
	 */
	public function all($db = null): array
	{
		return parent::all($db);
	}

	/**
	 * @return TaskTag|ActiveRecord|null
	 */
	public function one($db = null): ?TaskTag
	{
		return parent::one($db);
	}

	/**
	 * @return TaskTag|ActiveRecord|null
	 * @throws ModelNotFoundException
	 */
	public function oneOrThrow($db = null): TaskTag
	{
		return parent::oneOrThrow($db);
	}
}
