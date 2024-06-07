<?php

declare(strict_types=1);

namespace app\dto\QuestionAnswer;

use yii\base\BaseObject;

class CreateQuestionAnswerDto extends BaseObject
{
	public int     $field_id;
	public int     $category;
	public ?string $value;
}
