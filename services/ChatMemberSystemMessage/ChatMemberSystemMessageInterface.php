<?php

namespace app\services\ChatMemberSystemMessage;

interface ChatMemberSystemMessageInterface
{
	public static function create();

	public function setTemplate(string $template);

	public function getTemplateArgs(): array;

	public function toMessage(): string;

	public function validateOrThrow(): void;
}