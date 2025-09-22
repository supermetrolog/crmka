<?php

namespace app\models\forms\Integration;

use app\kernel\common\models\Form\Form;

class WhatsappLinkForm extends Form
{
	public int $phone;

	public function rules(): array
	{
		return [
			[['phone'], 'required'],
			[['phone'], 'integer'],
		];
	}
}
