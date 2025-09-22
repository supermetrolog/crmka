<?php
declare(strict_types=1);

namespace app\components\Telegram;

use app\components\Telegram\Interfaces\TelegramDeepLinkGeneratorInterface;
use app\helpers\Base64UrlHelper;
use app\helpers\StringHelper;
use DomainException;

/**
 * Поддерживает:
 * - web-ссылку:  https://t.me/<bot>?<param>=<payload>
 * - app-ссылку:  tg://resolve?domain=<bot>&<param>=<payload>
 */
final class TelegramDeepLinkGenerator implements TelegramDeepLinkGeneratorInterface
{
	public string $botName;
	public string $webBase;
	public string $appBase;
	public string $param;
	public string $prefer;

	public function build(string $rawPayload): string
	{
		$payload = base64_encode($rawPayload);

		$this->assertTelegramLimit($payload);

		return $this->prefer === 'app'
			? $this->makeApp($payload)
			: $this->makeWeb($payload);
	}

	public function forTicket(string $code): string
	{
		return $this->build($code);
	}

	public function both(string $rawPayload): array
	{
		$payload = Base64UrlHelper::encode($rawPayload);

		$this->assertTelegramLimit($payload);

		return [
			'web' => $this->makeWeb($payload),
			'app' => $this->makeApp($payload),
		];
	}

	private function makeWeb(string $payload): string
	{
		return rtrim($this->webBase, '/') . '/' . $this->botName . '?' . $this->param . '=' . $payload;
	}

	private function makeApp(string $payload): string
	{
		return $this->appBase . '?domain=' . $this->botName . '&' . $this->param . '=' . $payload;
	}

	private function assertTelegramLimit(string $payload): void
	{
		if (StringHelper::length($payload) > 64) {
			throw new DomainException('Слишком длинный payload для Telegram deep-link (max 64).');
		}
	}
}
