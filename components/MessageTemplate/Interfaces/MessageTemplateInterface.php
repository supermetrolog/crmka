<?php

declare(strict_types=1);

namespace app\components\MessageTemplate\Interfaces;

interface MessageTemplateInterface
{
	public function getKey(): string;

	public function createForm(): MessageTemplateFormInterface;

	public function getChannelTemplate(string $channelId): ChannelTemplateInterface;
}