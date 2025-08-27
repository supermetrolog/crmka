<?php

declare(strict_types=1);

namespace app\components\MessageTemplate\Templates\ResumeCompanyCooperation;

use app\components\MessageTemplate\Dto\ResumeCompanyCooperationMessageTemplateDto;
use app\components\MessageTemplate\Interfaces\ChannelTemplateInterface;
use app\components\MessageTemplate\RenderedMessage;

class ResumeCompanyCooperationMessengerTemplate implements ChannelTemplateInterface
{
	/**
	 * @param ResumeCompanyCooperationMessageTemplateDto $dto
	 */
	public function render($dto): RenderedMessage
	{
		$name = $dto->contact->getMediumName();

		$content = "Здравствуйте, {$name}!\n\n";

		return new RenderedMessage($content, 'text/plain');
	}
}