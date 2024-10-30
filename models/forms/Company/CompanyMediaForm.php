<?php

declare(strict_types=1);

namespace app\models\forms\Company;

use app\dto\Company\CompanyMediaDto;
use app\kernel\common\models\Form\Form;

class CompanyMediaForm extends Form
{
	public $files;
	public $logo;

	public function rules(): array
	{
		return [
			[['files'], 'file', 'extensions' => 'png, jpg, jpeg, pdf, xls, xlsx, ppt, pptp, doc, docx, txt', 'skipOnEmpty' => true, 'maxFiles' => 10],
			['logo', 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, jpeg, webp'],
		];
	}

	public function getDto(): CompanyMediaDto
	{
		return new CompanyMediaDto([
			'logo'  => $this->logo,
			'files' => $this->files
		]);
	}
}