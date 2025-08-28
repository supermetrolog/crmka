<?php

declare(strict_types=1);

namespace app\components\MessageTemplate\Forms;

use app\components\MessageTemplate\Dto\ResumeCompanyCooperationMessageTemplateDto;
use app\models\Company\Company;
use app\models\Contact;

class ResumeCompanyCooperationMessageTemplateForm extends MessageTemplateForm
{
	public $contact_id;
	public $company_id;

	public function rules(): array
	{
		return [
			[['contact_id', 'company_id'], 'required'],
			[['contact_id', 'company_id'], 'integer'],
			['contact_id', 'exist', 'targetClass' => Contact::class, 'targetAttribute' => 'id'],
			['company_id', 'exist', 'targetClass' => Company::class, 'targetAttribute' => 'id'],
		];
	}

	public function getDto(): ResumeCompanyCooperationMessageTemplateDto
	{
		return new ResumeCompanyCooperationMessageTemplateDto([
			'contact' => Contact::findOne($this->contact_id),
			'company' => Company::findOne($this->company_id),
		]);
	}

}