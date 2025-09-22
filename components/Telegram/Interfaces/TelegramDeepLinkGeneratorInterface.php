<?php

declare(strict_types=1);

namespace app\components\Telegram\Interfaces;

interface TelegramDeepLinkGeneratorInterface
{
	public function build(string $rawPayload): string;

	public function forTicket(string $code): string;

	public function both(string $rawPayload): array; // ['web' => ..., 'app' => ...]
}
