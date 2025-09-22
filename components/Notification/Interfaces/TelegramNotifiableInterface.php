<?php

declare(strict_types=1);

namespace app\components\Notification\Interfaces;

interface TelegramNotifiableInterface
{
	public function getTelegramChatId(): ?int;
}