<?php

declare(strict_types=1);

namespace app\commands;

use app\components\Integrations\Telegram\TelegramBotApiClient;
use app\kernel\common\controller\ConsoleController;
use Yii;
use yii\helpers\Json;
use yii\httpclient\Exception;

class TelegramController extends ConsoleController
{
	public TelegramBotApiClient $bot;
	public string               $webhookSecret;
	public string               $webhookUrl;
	public array                $allowedUpdates = [];

	public function __construct(
		string $id,
		$module,
		TelegramBotApiClient $bot,
		$config = [])
	{
		$this->bot = $bot;

		$this->webhookSecret = Yii::$app->params['crm_telegram_bot']['webhook']['secret'];
		$this->webhookUrl    = Yii::$app->params['crm_telegram_bot']['webhook']['url'];

		parent::__construct($id, $module, $config);
	}

	/**
	 * @throws Exception
	 */
	public function actionSetWebhook(): void
	{
		$response = $this->bot->setWebhook($this->webhookUrl, [
			'secret_token'    => $this->webhookSecret,
			'allowed_updates' => [
				'message', 'inline_query', 'chosen_inline_result', 'callback_query'
			]
		]);

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