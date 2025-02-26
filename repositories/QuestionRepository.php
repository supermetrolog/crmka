<?php

declare(strict_types=1);

namespace app\repositories;

use app\models\Question;
use app\models\SurveyQuestionAnswer;
use yii\db\ActiveQuery;

class QuestionRepository
{
	/**
	 * @return Question[]
	 */
	public function findAllBySurveyIdWithAnswers(int $surveyId): array
	{
		return Question::find()
		               ->joinWith([
			               'answers.surveyQuestionAnswer' => function (ActiveQuery $query) use ($surveyId) {
				               $query->where([SurveyQuestionAnswer::field('survey_id') => $surveyId]);
				               $query->with([
					               'tasks.user.userProfile',
					               'tasks.tags',
					               'tasks.createdByUser.userProfile',
					               'tasks.observers.user.userProfile',
					               'tasks.targetUserObserver',
					               'files'
				               ]);
			               }
		               ])
		               ->all();
	}
}
