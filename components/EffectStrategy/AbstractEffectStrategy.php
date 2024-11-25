<?php

namespace app\components\EffectStrategy;

use app\models\QuestionAnswer;
use app\models\Survey;

abstract class AbstractEffectStrategy implements EffectStrategyInterface
{
	public function handle(Survey $survey, QuestionAnswer $answer): void
	{
		$effectShouldBeProcess = $this->shouldBeProcessed($survey, $answer);

		if ($effectShouldBeProcess) {
			$this->process($survey);
		}
	}

	abstract public function shouldBeProcessed(Survey $survey, QuestionAnswer $answer): bool;

	/**
	 * @param ?mixed $additionalData
	 */
	abstract public function process(Survey $survey, $additionalData = null): void;
}