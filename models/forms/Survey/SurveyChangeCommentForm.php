<?php

declare(strict_types=1);

namespace app\models\forms\Survey;

use app\kernel\common\models\Form\Form;

class SurveyChangeCommentForm extends Form
{
	public $comment;

	public function rules(): array
	{
		return [
			[['comment'], 'string', 'max' => 1024],
		];
	}

	public function attributeLabels(): array
	{
		return [
			'comment' => 'Комментарий',
		];
	}
}