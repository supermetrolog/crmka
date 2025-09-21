<?php
declare(strict_types=1);

namespace app\components\Telegram;

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
	public function sendMessage(int $chatId, string $text, array $params = []): array
	{
		$payload = ArrayHelper::merge(
			[
				'chat_id'                  => $chatId,
				'text'                     => $text,
				'parse_mode'               => 'HTML',
				'disable_web_page_preview' => true,
			],
			$params
		);

		return $this->http->post('sendMessage', $payload)->send()->getData();
	}

	/**
	 * @throws Exception
	 */
	public function setWebhook(string $url, ?string $secretToken = null)
	{
		$payload = [
			'url' => $url
		];

		if ($secretToken) {
			$payload['secret_token'] = $secretToken;
		}

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
