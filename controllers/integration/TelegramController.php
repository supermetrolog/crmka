<?php
declare(strict_types=1);

namespace app\controllers\integration;

use app\kernel\common\controller\AppController;
use app\kernel\common\models\exceptions\SaveModelException;
use app\kernel\web\http\responses\SuccessResponse;
use app\resources\Telegram\StartTelegramLinkResource;
use app\resources\Telegram\StatusTelegramLinkResource;
use app\usecases\Telegram\TelegramLinkService;
use yii\base\Exception;

final class TelegramController extends AppController
{
	protected TelegramLinkService $linkService;

	public function __construct(
		$id,
		$module,
		TelegramLinkService $linkService,
		$config = []
	)
	{
		$this->linkService = $linkService;

		parent::__construct($id, $module, $config);
	}

	/**
	 * @throws SaveModelException
	 * @throws Exception
	 */
	public function actionStart(): StartTelegramLinkResource
	{
		$dto = $this->linkService->createTicket($this->user->identity);

		return new StartTelegramLinkResource($dto);
	}

	public function actionStatus(): StatusTelegramLinkResource
	{
		$dto = $this->linkService->getStatusForUser($this->user->identity);

		return new StatusTelegramLinkResource($dto);
	}

	/**
	 * @throws SaveModelException
	 */
	public function actionRevoke(): SuccessResponse
	{
		$this->linkService->revokeByUser($this->user->identity);

		return $this->success('Телеграмм аккаунт отвязан');
	}
}
