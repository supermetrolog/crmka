<?php

declare(strict_types=1);

namespace app\components\Notification\Drivers\Telegram;

interface TelegramNotifiableInterface
{
	public function getTelegramId(): string;
}