<?php

declare(strict_types=1);

namespace app\dto\SurveyQuestionAnswer;

use app\models\QuestionAnswer;
use app\models\Survey;
use yii\base\BaseObject;

class CreateSurveyQuestionAnswerDto extends BaseObject
{
	public QuestionAnswer $question_answer;
	public Survey         $survey;
	public ?string        $value;
}
