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
		               ->innerJoinWith([
			               'answers.surveyQuestionAnswer' => function (ActiveQuery $query) use ($surveyId) {
				               $query->andOnCondition([SurveyQuestionAnswer::field('survey_id') => $surveyId]);
			               }
		               ])
		               ->with([
			               'answers.effects',
			               'answers.field',
			               'answers.surveyQuestionAnswer.tasks.user.userProfile',
			               'answers.surveyQuestionAnswer.tasks.tags',
			               'answers.surveyQuestionAnswer.tasks.createdByUser.userProfile',
			               'answers.surveyQuestionAnswer.tasks.observers.user.userProfile',
			               'answers.surveyQuestionAnswer.tasks.targetUserObserver',
			               'answers.surveyQuestionAnswer.files',
			               'answers.surveyQuestionAnswer.field',
		               ])
		               ->all();
	}
}
