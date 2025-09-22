<?php
declare(strict_types=1);

namespace app\components\Integrations\Telegram;

use app\helpers\ArrayHelper;
use yii\httpclient\Client;
use yii\httpclient\Exception;

final class TelegramBotApiClient
{
	private Client $http;

	public function __construct(string $apiUrl)
	{
		$this->http = new Client(['baseUrl' => $apiUrl]);
	}

	/**
	 * @throws Exception
	 */
	public function send(int $chatId, array $params): array
	{
		$payload = ArrayHelper::merge(
			[
				'chat_id' => $chatId,
			],
			$params
		);

		return $this->http->post('sendMessage', $payload)->send()->getData();
	}

	/**
	 * @throws Exception
	 */
	public function sendMessage(int $chatId, string $text, array $params = []): array
	{
		return $this->send($chatId, ArrayHelper::merge(['text' => $text], $params));
	}

	/**
	 * @throws Exception
	 */
	public function setWebhook(string $url, array $config = [])
	{
		$payload = ArrayHelper::merge(['url' => $url], $config);

		return $this->http->post('setWebhook', $payload)->send()->getData();
	}

	/**
	 * @throws Exception
	 */
	public function getWebhookInfo(): array
	{
		return $this->http->post('getWebhookInfo')->send()->getData();
	}

	/**
	 * @throws Exception
	 */
	public function deleteWebhook(): array
	{
		return $this->http->post('deleteWebhook')->send()->getData();
	}
}
