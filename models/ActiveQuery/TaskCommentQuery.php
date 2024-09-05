<?php

namespace app\models\ActiveQuery;

use app\kernel\common\models\AQ\AQ;
use app\kernel\common\models\AQ\SoftDeleteTrait;
use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\models\TaskComment;
use yii\db\ActiveRecord;

class TaskCommentQuery extends AQ
{
	use SoftDeleteTrait;

	/**
	 * @return TaskComment[]|ActiveRecord[]
	 */
	public function all($db = null): array
	{
		return parent::all($db);
	}

	/**
	 * @return TaskComment|ActiveRecord|null
	 */
	public function one($db = null): ?TaskComment
	{
		return parent::one($db);
	}

	/**
	 * @return TaskComment|ActiveRecord|null
	 * @throws ModelNotFoundException
	 */
	public function oneOrThrow($db = null): TaskComment
	{
		return parent::oneOrThrow($db);
	}
}
