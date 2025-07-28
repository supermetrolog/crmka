<?php

namespace app\enum\EntityMessageLink;

use app\enum\AbstractEnum;

class EntityMessageLinkKindEnum extends AbstractEnum
{
	public const COMMENT = 'comment';
	public const PIN     = 'pin';
	public const NOTE    = 'note';

	public static function labels(): array
	{
		return [
			self::COMMENT => 'Комментарий',
			self::PIN     => 'Закреп',
			self::NOTE    => 'Заметка',
		];
	}
}