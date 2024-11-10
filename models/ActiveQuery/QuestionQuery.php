<?php

namespace app\models\ActiveQuery;

use app\kernel\common\models\AQ\AQ;
use app\kernel\common\models\AQ\SoftDeleteTrait;
use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\models\Question;
use yii\db\ActiveRecord;

/**
 * This is the ActiveQuery class for [[\app\models\Question]].
 *
 * @see Question
 */
class QuestionQuery extends AQ
{
	use SoftDeleteTrait;

	/**
	 * @return Question[]|ActiveRecord[]
	 */
	public function all($db = null): array
	{
		return parent::all($db);
	}

	/**
	 * @return Question|ActiveRecord|null
	 */
	public function one($db = null): ?Question
	{
		return parent::one($db);
	}

	/**
	 * @return Question|ActiveRecord
	 * @throws ModelNotFoundException
	 */
	public function oneOrThrow($db = null): Question
	{
		return parent::oneOrThrow($db);
	}

	public function byGroup(string $group): self
	{
		return $this->andWhere([$this->field('group') => $group]);
	}
}
