<?php

declare(strict_types=1);

namespace app\components\MessageTemplate\Dto;

use app\models\Company\Company;
use app\models\Contact;

class ResumeCompanyCooperationMessageTemplateDto extends MessageTemplateDto
{
	public Company $company;
	public Contact $contact;
}