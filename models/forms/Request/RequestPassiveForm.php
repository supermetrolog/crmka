<?php

declare(strict_types=1);

namespace app\models\forms\Request;

use app\dto\Request\PassiveRequestDto;
use app\kernel\common\models\Form\Form;

class RequestPassiveForm extends Form
{
	public $passive_why;
	public $passive_why_comment;

	public function rules(): array
	{
		return [
			[['passive_why'], 'required'],
			[['passive_why'], 'integer'],
			[['passive_why_comment'], 'string', 'max' => 255]
		];
	}

	public function getDto(): PassiveRequestDto
	{
		return new PassiveRequestDto([
			'passive_why'         => $this->passive_why,
			'passive_why_comment' => $this->passive_why_comment
		]);
	}
}