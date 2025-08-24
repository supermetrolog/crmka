<?php

declare(strict_types=1);

namespace app\components\MessageTemplate\Interfaces;

use app\components\MessageTemplate\RenderedMessage;

interface ChannelTemplateInterface
{
	public function render(MessageTemplateDtoInterface $dto): RenderedMessage;
}
