<?php

declare(strict_types=1);

namespace app\models\forms\Company;

use app\dto\Company\DisableCompanyDto;
use app\kernel\common\models\Form\Form;
use app\models\Company\Company;

class CompanyDisableForm extends Form
{
	public $passive_why;

	public $disable_requests = true;
	public $disable_contacts = true;

	public function rules(): array
	{
		return [
			['passive_why', 'required'],
			['passive_why', 'integer'],
			['passive_why', 'in', 'range' => Company::getPassiveWhyOptions()],
			[['disable_requests', 'disable_contacts'], 'boolean']
		];
	}

	public function attributeLabels(): array
	{
		return [
			'passive_why'      => 'Причина',
			'disable_requests' => 'Флаг архивации запросов',
			'disable_contacts' => 'Флаг архивации контактов',
		];
	}

	public function getDto(): DisableCompanyDto
	{
		return new DisableCompanyDto([
			'passive_why'      => $this->passive_why,
			'disable_requests' => $this->disable_requests,
			'disable_contacts' => $this->disable_contacts
		]);
	}
}