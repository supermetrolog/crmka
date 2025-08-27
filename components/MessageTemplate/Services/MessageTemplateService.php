<?php

declare(strict_types=1);

namespace app\components\MessageTemplate\Services;

use app\components\MessageTemplate\Enums\ChannelEnum;
use app\components\MessageTemplate\Interfaces\MessageTemplateDtoInterface;
use app\components\MessageTemplate\Interfaces\MessageTemplateInterface;
use app\components\MessageTemplate\RenderedMessage;

class MessageTemplateService
{
	public function render(MessageTemplateInterface $template, MessageTemplateDtoInterface $dto, string $channelId = ChannelEnum::EMAIL): RenderedMessage
	{
		return $template->getChannelTemplate($channelId)->render($dto);
	}
}