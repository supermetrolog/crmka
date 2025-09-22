<?php
declare(strict_types=1);

namespace app\components\Integrations\Whatsapp;

use app\components\Integrations\Whatsapp\Models\Response\WContactCheckResponse;
use app\components\Integrations\Whatsapp\Models\Response\WContactInfoResponse;
use app\helpers\ArrayHelper;
use yii\base\InvalidConfigException;
use yii\httpclient\Client;
use yii\httpclient\Exception;
use yii\httpclient\Request;

final class WhatsappApiClient
{
	protected Client $http;
	protected string $token;
	protected string $profileId;

	public function __construct(string $apiUrl, string $token, string $profileId)
	{
		$this->http = new Client([
			'baseUrl'        => $apiUrl,
			'responseConfig' => [
				'format' => Client::FORMAT_JSON
			]
		]);

		$this->token     = $token;
		$this->profileId = $profileId;
	}

	/**
	 * @throws InvalidConfigException
	 */
	protected function request(string $method, $url, $data = [], array $headers = [], array $options = []): Request
	{
		$request = $this->http->createRequest()
		                      ->setFormat(Client::FORMAT_JSON)
		                      ->addHeaders(['Authorization' => $this->token]);

		$preparedUrl = ArrayHelper::merge(ArrayHelper::toArray($url), ['profile_id' => $this->profileId]);

		$request->setMethod($method)
		        ->setUrl($preparedUrl)
		        ->addHeaders($headers)
		        ->addOptions($options);

		if (is_array($data)) {
			$request->setData($data);
		} else {
			$request->setContent($data);
		}

		return $request;
	}

	/**
	 * @throws InvalidConfigException
	 */
	protected function get($url, array $data = [], array $headers = [], array $options = []): Request
	{
		return $this->request('GET', $url, $data, $headers, $options);
	}

	/**
	 * @throws InvalidConfigException
	 */
	protected function post($url, array $data = [], array $headers = [], array $options = []): Request
	{
		return $this->request('POST', $url, $data, $headers, $options);
	}

	/**
	 * @throws InvalidConfigException
	 * @throws Exception
	 */
	public function checkPhone(int $phone): WContactCheckResponse
	{
		$response = $this->get(['/sync/contact/check', 'phone' => $phone])->send();

		return new WContactCheckResponse($response->getData());
	}

	/**
	 * @param int $userId Profile ID or Contact Phone or Group ID
	 *
	 * @throws InvalidConfigException
	 * @throws Exception
	 */
	public function getContactInfo(int $userId): WContactInfoResponse
	{
		$response = $this->get(['/sync/contact/info', 'user_id' => $userId])->send();

		return new WContactInfoResponse($response->getData());
	}

	/**
	 * @throws Exception
	 * @throws InvalidConfigException
	 */
	public function sendMessage(string $phone, string $message): void
	{
		$this->post('/async/message/send', ['recipient' => $phone, 'body' => $message])->send();
	}
}
