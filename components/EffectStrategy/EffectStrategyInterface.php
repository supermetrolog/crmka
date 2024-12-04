<?php

namespace app\components\EffectStrategy;


use app\models\ChatMemberMessage;
use app\models\QuestionAnswer;
use app\models\Survey;
use app\models\SurveyQuestionAnswer;

interface EffectStrategyInterface
{
	public function handle(Survey $survey, QuestionAnswer $answer, ChatMemberMessage $surveyChatMemberMessage);

	public function shouldBeProcessed(Survey $survey, QuestionAnswer $answer): bool;

	public function process(Survey $survey, SurveyQuestionAnswer $surveyQuestionAnswer, ChatMemberMessage $surveyChatMemberMessage);
}