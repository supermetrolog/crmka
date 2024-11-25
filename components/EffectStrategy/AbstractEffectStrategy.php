<?php

namespace app\components\EffectStrategy;

use app\models\QuestionAnswer;
use app\models\Survey;
use app\models\SurveyQuestionAnswer;

abstract class AbstractEffectStrategy implements EffectStrategyInterface
{
	public function handle(Survey $survey, QuestionAnswer $answer): void
	{
		$effectShouldBeProcess = $this->shouldBeProcessed($survey, $answer);

		if ($effectShouldBeProcess) {
			$this->process($survey, $answer->surveyQuestionAnswer);
		}
	}

	abstract public function shouldBeProcessed(Survey $survey, QuestionAnswer $answer): bool;

	abstract public function process(Survey $survey, SurveyQuestionAnswer $surveyQuestionAnswer): void;
}