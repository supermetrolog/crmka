<?php

namespace app\models\ActiveQuery;

use app\kernel\common\models\AQ\AQ;
use app\kernel\common\models\AQ\SoftDeleteTrait;
use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\models\TaskTaskTag;
use yii\db\ActiveRecord;

class TaskTaskTagQuery extends AQ
{
	use SoftDeleteTrait;

	/**
	 * @return TaskTaskTag[]|ActiveRecord[]
	 */
	public function all($db = null): array
	{
		return parent::all($db);
	}

	/**
	 * @return TaskTaskTag|ActiveRecord|null
	 */
	public function one($db = null): ?TaskTaskTag
	{
		return parent::one($db);
	}

	/**
	 * @return TaskTaskTag|ActiveRecord|null
	 * @throws ModelNotFoundException
	 */
	public function oneOrThrow($db = null): TaskTaskTag
	{
		return parent::oneOrThrow($db);
	}
}
