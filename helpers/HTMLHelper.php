<?php

declare(strict_types=1);

namespace app\helpers;

class HTMLHelper
{
	public static function deleted(string $text): string
	{
		return StringHelper::join(StringHelper::SYMBOL_EMPTY, '<del>', $text, '</del>');
	}

	public static function bold(string $text): string
	{
		return StringHelper::join(StringHelper::SYMBOL_EMPTY, '<b>', $text, '</b>');
	}
}