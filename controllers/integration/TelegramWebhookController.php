<?php
declare(strict_types=1);

namespace app\controllers\integration;

use app\components\Telegram\Models\TUpdate;
use app\components\Telegram\TelegramBotApiClient;
use app\kernel\common\controller\AppController;
use app\usecases\Telegram\TelegramWebhookService;
use Throwable;
use Yii;
use yii\helpers\Json;
use yii\web\BadRequestHttpException;
use yii\web\Response;

final class TelegramWebhookController extends AppController
{
	protected array               $exceptAuthActions = ['handle'];
	public TelegramWebhookService $service;
	public TelegramBotApiClient   $bot;
	public string                 $secretHeader;
	public string                 $webhookSecret;

	public function __construct(
		$id,
		$module,
		TelegramBotApiClient $bot,
		TelegramWebhookService $service,
		$config = []
	)
	{
		$this->bot     = $bot;
		$this->service = $service;

		// TODO: В controllerMap не сетится.. Разобраться

		$this->secretHeader  = Yii::$app->params['crm_telegram_bot']['webhook']['secretHeader'];
		$this->webhookSecret = Yii::$app->params['crm_telegram_bot']['webhook']['secret'];

		parent::__construct($id, $module, $config);
	}

	public function beforeAction($action): bool
	{
		$this->assertWebhookSecret();

		return parent::beforeAction($action);
	}

	/**
	 * @throws BadRequestHttpException
	 */
	private function assertWebhookSecret(): void
	{
		$secret = $this->request->getHeaders()->get($this->secretHeader);

		if ($secret !== $this->webhookSecret) {
			throw new BadRequestHttpException('Invalid secret');
		}
	}

	public function actionHandle(): Response
	{
		$payload = Json::decode($this->request->getRawBody()) ?? [];

		try {
			$this->service->handleUpdate(new TUpdate($payload));
		} catch (Throwable $e) {
			Yii::error(['message' => $e->getMessage(), 'payload' => $payload], 'telegram.webhook');
		}

		return $this->asJson(['ok' => true]);
	}
}
