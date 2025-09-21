<?php

namespace app\enum\Telegram;

use app\enum\AbstractEnum;

class TelegramUpdateCommandEnum extends AbstractEnum
{
	public const START  = '/start';
	public const STATUS = '/status';
	public const REVOKE = '/revoke';
}