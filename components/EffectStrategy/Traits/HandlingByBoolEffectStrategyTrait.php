<?php

namespace app\components\EffectStrategy\Traits;

use app\models\QuestionAnswer;
use app\models\Survey;

trait HandlingByBoolEffectStrategyTrait
{
	/** Override it in child classes if needed process answers with another value */
	protected function getProcessedValue(): bool
	{
		return true;
	}

	public function shouldBeProcessed(Survey $survey, QuestionAnswer $answer): bool
	{
		return $answer->surveyQuestionAnswer->getMaybeBool() === $this->getProcessedValue();
	}
}