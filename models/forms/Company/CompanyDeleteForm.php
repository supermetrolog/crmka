<?php

declare(strict_types=1);

namespace app\models\forms\Company;

use app\dto\Company\DeleteCompanyDto;
use app\kernel\common\models\Form\Form;
use app\models\Company\Company;

class CompanyDeleteForm extends Form
{
	public $passive_why;
	public $comment;

	public $disable_requests = true;
	public $disable_contacts = true;

	public function rules(): array
	{
		return [
			['passive_why', 'required'],
			['passive_why', 'in', 'range' => Company::getPassiveWhyOptions()],
			['comment', 'string', 'max' => 255],
			[['disable_requests', 'disable_contacts'], 'boolean']
		];
	}

	public function attributeLabels(): array
	{
		return [
			'passive_why'      => 'Причина',
			'comment'          => 'Комментарий',
			'disable_requests' => 'Флаг архивации запросов',
			'disable_contacts' => 'Флаг архивации контактов',
		];
	}

	public function getDto(): DeleteCompanyDto
	{
		return new DeleteCompanyDto([
			'passive_why'      => $this->passive_why,
			'comment'          => $this->comment,
			'disable_requests' => $this->disable_requests,
			'disable_contacts' => $this->disable_contacts
		]);
	}
}