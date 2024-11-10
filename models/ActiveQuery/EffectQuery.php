<?php

namespace app\models\ActiveQuery;

use app\kernel\common\models\AQ\AQ;
use app\models\Effect;
use app\models\SurveyQuestionAnswer;

/**
 * This is the ActiveQuery class for [[SurveyQuestionAnswer]].
 *
 * @see SurveyQuestionAnswer
 */
class EffectQuery extends AQ
{
	/**
	 * @return Effect[]
	 */
	public function all($db = null): array
	{
		return parent::all($db);
	}

	/**
	 * @return ?Effect
	 */
	public function one($db = null): ?Effect
	{
		/** @var ?Effect */
		return parent::one($db);
	}

	public function oneOrThrow($db = null): Effect
	{
		/** @var Effect */
		return parent::oneOrThrow($db);
	}

	public function byKind(string $kind): EffectQuery
	{
		return $this->andWhere([$this->field('kind') => $kind]);
	}
}
