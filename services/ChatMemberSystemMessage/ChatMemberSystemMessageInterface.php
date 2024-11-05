<?php

namespace app\services\ChatMemberSystemMessage;

interface ChatMemberSystemMessageInterface
{
	public static function create();

	public function toMessage(): string;

	public function validateOrThrow(): void;
}