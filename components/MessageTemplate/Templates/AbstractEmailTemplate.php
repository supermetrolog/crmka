<?php

declare(strict_types=1);

namespace app\components\MessageTemplate\Templates;

use app\components\MessageTemplate\Interfaces\ChannelTemplateInterface;
use app\components\MessageTemplate\Interfaces\EmailTwigEnvironmentInterface;
use app\components\MessageTemplate\Interfaces\MessageTemplateContextInterface;
use app\components\MessageTemplate\Interfaces\MessageTemplateDtoInterface;
use app\components\MessageTemplate\RenderedMessage;

abstract class AbstractEmailTemplate implements ChannelTemplateInterface
{
	protected EmailTwigEnvironmentInterface $environment;

	public function __construct(EmailTwigEnvironmentInterface $environment)
	{
		$this->environment = $environment;
	}

	public function render(MessageTemplateDtoInterface $dto): RenderedMessage
	{
		$templateName = $this->getTemplateName();
		$context      = $this->prepareContext($dto);

		$htmlContent = $this->environment->render($templateName, $context->toArray());

		return new RenderedMessage($htmlContent, 'text/html');
	}

	abstract protected function getTemplateName(): string;

	abstract protected function prepareContext(MessageTemplateDtoInterface $dto): MessageTemplateContextInterface;
}