<?php

declare(strict_types=1);

namespace app\commands;

use app\components\Telegram\TelegramBotApiClient;
use app\kernel\common\controller\ConsoleController;
use yii\helpers\Json;
use yii\httpclient\Exception;

class TelegramController extends ConsoleController
{
	public TelegramBotApiClient $bot;
	public string               $webhookSecret;
	public string               $webhookUrl;

	public function __construct(
		string $id,
		$module,
		TelegramBotApiClient $bot,
		$config = [])
	{
		$this->bot = $bot;

		parent::__construct($id, $module, $config);
	}

	/**
	 * @throws Exception
	 */
	public function actionSetWebhook(): void
	{
		$response = $this->bot->setWebhook($this->webhookUrl, $this->webhookSecret);

		$this->comment(Json::encode($response));
	}

	/**
	 * @throws Exception
	 */
	public function actionGetWebhookInfo(): void
	{
		$response = $this->bot->getWebhookInfo();

		$this->comment(Json::encode($response));
	}

	/**
	 * @throws Exception
	 */
	public function actionDeleteWebhook(): void
	{
		$response = $this->bot->deleteWebhook();

		$this->comment(Json::encode($response));
	}
}