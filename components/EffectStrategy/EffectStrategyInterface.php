<?php

namespace app\components\EffectStrategy;


use app\models\QuestionAnswer;
use app\models\Survey;

interface EffectStrategyInterface
{
	public function handle(Survey $survey, QuestionAnswer $answer);
}