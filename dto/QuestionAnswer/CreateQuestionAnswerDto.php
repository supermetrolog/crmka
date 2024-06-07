<?php

declare(strict_types=1);

namespace app\dto\QuestionAnswer;

use app\models\Field;
use yii\base\BaseObject;

class CreateQuestionAnswerDto extends BaseObject
{
	public Field   $field;
	public int     $category;
	public ?string $value;
}
