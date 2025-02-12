<?php

namespace app\models\ActiveQuery;

use app\kernel\common\models\AQ\AQ;
use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\models\SurveyQuestionAnswer;
use yii\base\ErrorException;
use yii\db\ActiveRecord;

/**
 * This is the ActiveQuery class for [[SurveyQuestionAnswer]].
 *
 * @see SurveyQuestionAnswer
 */
class SurveyQuestionAnswerQuery extends AQ
{
	/**
	 * @return SurveyQuestionAnswer[]|ActiveRecord[]
	 */
	public function all($db = null): array
	{
		return parent::all($db);
	}

	/**
	 * @return SurveyQuestionAnswer|ActiveRecord|null
	 */
	public function one($db = null): ?SurveyQuestionAnswer
	{
		return parent::one($db);
	}

	/**
	 * @return SurveyQuestionAnswer|ActiveRecord
	 * @throws ModelNotFoundException
	 */
	public function oneOrThrow($db = null): SurveyQuestionAnswer
	{
		return parent::oneOrThrow($db);
	}

	/**
	 * @throws ErrorException
	 */
	public function bySurveyId(int $surveyId): SurveyQuestionAnswerQuery
	{
		return $this->andWhere([SurveyQuestionAnswer::field('survey_id') => $surveyId]);
	}

	/**
	 * @throws ErrorException
	 */
	public function byQuestionAnswerId(int $questionAnswerId): SurveyQuestionAnswerQuery
	{
		return $this->andWhere([SurveyQuestionAnswer::field('question_answer_id') => $questionAnswerId]);
	}
}
