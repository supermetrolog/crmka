<?php

declare(strict_types=1);

namespace app\dto\QuestionAnswer;

use yii\base\BaseObject;

class UpdateQuestionAnswerDto extends BaseObject
{
	public int     $question_id;
	public int     $field_id;
	public string  $category;
	public ?string $value;
	public ?string $message;
	public array   $effectIds;
}