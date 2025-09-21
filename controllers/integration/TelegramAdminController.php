<?php
declare(strict_types=1);

namespace app\controllers\integration;

use app\kernel\common\controller\AppController;
use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\kernel\common\models\exceptions\SaveModelException;
use app\kernel\common\models\exceptions\ValidateException;
use app\kernel\web\http\responses\SuccessResponse;
use app\models\search\UserTelegramLinkSearch;
use app\models\search\UserTelegramLinkTicketSearch;
use app\repositories\UserRepository;
use app\repositories\UserTelegramLinkRepository;
use app\resources\Telegram\UserTelegramLinkResource;
use app\resources\Telegram\UserTelegramLinkTicketResource;
use app\usecases\Telegram\TelegramLinkService;
use ErrorException;
use yii\data\ActiveDataProvider;

final class TelegramAdminController extends AppController
{
	protected TelegramLinkService        $linkService;
	protected UserRepository             $userRepository;
	protected UserTelegramLinkRepository $linkRepository;

	public function __construct(
		$id,
		$module,
		TelegramLinkService $linkService,
		UserRepository $userRepository,
		UserTelegramLinkRepository $linkRepository,
		$config = []
	)
	{
		$this->linkService    = $linkService;
		$this->userRepository = $userRepository;
		$this->linkRepository = $linkRepository;

		parent::__construct($id, $module, $config);
	}

	/**
	 * @throws ValidateException
	 * @throws ErrorException
	 */
	public function actionList(): ActiveDataProvider
	{
		$searcher = new UserTelegramLinkSearch();

		return UserTelegramLinkResource::fromDataProvider($searcher->search($this->request->get()));
	}

	/**
	 * @throws ValidateException
	 * @throws ErrorException
	 */
	public function actionTickets(): ActiveDataProvider
	{
		$searcher = new UserTelegramLinkTicketSearch();

		return UserTelegramLinkTicketResource::fromDataProvider($searcher->search($this->request->get()));
	}

	/**
	 * @throws SaveModelException
	 * @throws ModelNotFoundException
	 */
	public function actionRevokeUser(int $id): SuccessResponse
	{
		$user = $this->userRepository->findOneOrThrow($id);

		$this->linkService->revokeByUser($user);

		return $this->success('Телеграмм аккаунт отвязан');
	}

	/**
	 * @throws SaveModelException
	 * @throws ModelNotFoundException
	 */
	public function actionRevokeLink(int $id): SuccessResponse
	{
		$link = $this->linkRepository->findOneOrThrow($id);

		$this->linkService->revoke($link);

		return $this->success('Телеграмм аккаунт отвязан');
	}
}
