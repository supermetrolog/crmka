<?php
declare(strict_types=1);

namespace app\components\Whatsapp;

use app\helpers\ArrayHelper;
use yii\base\InvalidConfigException;
use yii\httpclient\Client;
use yii\httpclient\Request;

final class WhatsappApiClient
{
	protected Client $http;
	protected string $token;
	protected string $profileId;

	public function __construct(string $apiUrl, string $token, string $profileId)
	{
		$this->http = new Client(['baseUrl' => $apiUrl, 'requestConfig' => [
			'headers' => [
				'Authorization' => "Bearer $token",
				'Accept'        => 'application/json'
			]
		]]);

		$this->token     = $token;
		$this->profileId = $profileId;
	}

	/**
	 * @throws InvalidConfigException
	 */
	protected function request(string $method, $url, $data = [], array $headers = [], array $options = []): Request
	{
		$request = $this->http->createRequest()
		                      ->addHeaders(['Authorization' => "Bearer $this->token"]);

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
}
