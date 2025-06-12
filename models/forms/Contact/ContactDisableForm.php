<?php

declare(strict_types=1);

namespace app\models\forms\Contact;

use app\dto\Contact\DisableContactDto;
use app\kernel\common\models\Form\Form;
use app\models\Contact;

class ContactDisableForm extends Form
{
	public $passive_why;
	public $passive_why_comment;

	public function rules(): array
	{
		return [
			['passive_why', 'required'],
			['passive_why', 'integer'],
			['passive_why', 'in', 'range' => Contact::getPassiveWhyOptions()],
			['passive_why_comment', 'string', 'max' => 255],
		];
	}

	public function attributeLabels(): array
	{
		return [
			'passive_why'         => 'Причина',
			'passive_why_comment' => 'Комментарий'
		];
	}

	public function getDto(): DisableContactDto
	{
		return new DisableContactDto([
			'passive_why'         => $this->passive_why,
			'passive_why_comment' => $this->passive_why_comment
		]);
	}
}