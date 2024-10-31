<?php

declare(strict_types=1);

namespace app\models\forms\Company;

use app\kernel\common\models\Form\Form;

class CompanyLogoForm extends Form
{
	public $logo;

	public function rules(): array
	{
		return [
			['logo', 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, jpeg, webp'],
		];
	}
}