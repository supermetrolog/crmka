<?php

declare(strict_types=1);

namespace app\dto\SurveyQuestionAnswer;

use yii\base\BaseObject;

class CreateSurveyQuestionAnswerDto extends BaseObject
{
	public int $question_answer_id;
	public int $survey_id;
	
	/**
	 * @var mixed
	 */
	public $value;
}
