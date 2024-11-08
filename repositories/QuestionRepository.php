<?php

declare(strict_types=1);

namespace app\repositories;

use app\models\Question;
use app\models\SurveyQuestionAnswer;
use yii\db\ActiveQuery;

class QuestionRepository
{

	/** @return Question[] */
	public function getWithAnswersBySurveyId(int $surveyId): array
	{
		return Question::find()
		               ->joinWith([
			               'answers.surveyQuestionAnswer' => function (ActiveQuery $query) use ($surveyId) {
				               $query->where([SurveyQuestionAnswer::field('survey_id') => $surveyId]);
			               }
		               ])->all();
	}
}
