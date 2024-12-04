<?php

declare(strict_types=1);

namespace app\helpers;

class HTMLHelper
{
	public const TAG_LINE_BREAK = '<br>';

	public static function deleted(string $text): string
	{
		return StringHelper::join(StringHelper::SYMBOL_EMPTY, '<del>', $text, '</del>');
	}

	public static function bold(string $text): string
	{
		return StringHelper::join(StringHelper::SYMBOL_EMPTY, '<b>', $text, '</b>');
	}

	/**
	 * Generate font awesome icons with `i` tag
	 *
	 * @param 'solid'|'regular' $prefix
	 * @param string            $name
	 *
	 * @return string
	 * @see      https://fontawesome.com/icons
	 */
	public static function icon(string $prefix, string $name): string
	{
		return StringHelper::join(StringHelper::SYMBOL_EMPTY, '<i class="fa-', $prefix, ' fa-', $name, '"></i>');
	}

	public static function squareMeters(string $value): string
	{
		return StringHelper::join(StringHelper::SYMBOL_SPACE, $value, 'м<sup>2</sup>');
	}
}