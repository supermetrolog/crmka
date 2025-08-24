<?php

declare(strict_types=1);

namespace app\components\MessageTemplate\Templates\ResumeCompanyCooperation;

use app\components\MessageTemplate\Enums\ChannelEnum;
use app\components\MessageTemplate\Enums\MessageTemplateEnum;
use app\components\MessageTemplate\Forms\ResumeCompanyCooperationMessageTemplateForm;
use app\components\MessageTemplate\Interfaces\MessageTemplateFormInterface;
use app\components\MessageTemplate\Templates\AbstractMessageTemplate;

class ResumeCompanyCooperationMessageTemplate extends AbstractMessageTemplate
{
	protected array $templatesByChannel = [
		ChannelEnum::EMAIL     => ResumeCompanyCooperationEmailTemplate::class,
		ChannelEnum::MESSENGER => ResumeCompanyCooperationMessengerTemplate::class
	];

	protected string $key = MessageTemplateEnum::RESUME_COMPANY_COOPERATION;

	public function createForm(): MessageTemplateFormInterface
	{
		return new ResumeCompanyCooperationMessageTemplateForm();
	}
}