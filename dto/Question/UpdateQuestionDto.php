<?php

declare(strict_types=1);

namespace app\dto\Question;

use yii\base\BaseObject;

class UpdateQuestionDto extends BaseObject
{
	public string  $text;
	public ?string $group;
	public ?string $template;
}