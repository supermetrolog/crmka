<?php

namespace app\enum\Telegram;

use app\enum\AbstractEnum;

class TelegramUpdateCommandEnum extends AbstractEnum
{
	public const START  = '/start';
	public const LINK   = '/link';
	public const REVOKE = '/revoke';
}