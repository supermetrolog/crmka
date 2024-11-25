<?php

namespace app\components\EffectStrategy;


use app\models\QuestionAnswer;
use app\models\Survey;

interface EffectStrategyInterface
{
	public function handle(Survey $survey, QuestionAnswer $answer);

	public function shouldBeProcessed(Survey $survey, QuestionAnswer $answer);

	/**
	 * @param ?mixed $additionalData
	 */
	public function process(Survey $survey, $additionalData = null);
}