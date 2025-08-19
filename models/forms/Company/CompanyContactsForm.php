<?php

declare(strict_types=1);

namespace app\models\forms\Company;

use app\dto\Company\CompanyContactsDto;
use app\helpers\StringHelper;
use app\kernel\common\models\Form\Form;

class CompanyContactsForm extends Form
{
	public $websites;
	public $emails;

	public function rules(): array
	{
		return [
			[
				'websites',
				'validateParameters',
				'params'      => [
					'parameter' => 'website'
				],
				'skipOnEmpty' => true
			],
			[
				'emails',
				'validateParameters',
				'params'      => [
					'parameter' => 'email'
				],
				'skipOnEmpty' => true
			]
		];
	}

	public function validateParameters($attribute, $params)
	{
		foreach ($this->{$attribute} as $parameter) {
			if (!isset($parameter[$params['parameter']]) || !StringHelper::isString($parameter[$params['parameter']])) {
				$this->addError($attribute, "Некорректно указан $parameter[parameter]");
			}
		}
	}

	public function getDto(): CompanyContactsDto
	{
		return new CompanyContactsDto([
			'websites' => $this->websites,
			'emails'   => $this->emails
		]);
	}
}