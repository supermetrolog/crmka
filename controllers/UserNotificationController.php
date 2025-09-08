<?php

namespace app\controllers;

use app\dto\UserNotification\ProcessUserNotificationActionDto;
use app\helpers\DateTimeHelper;
use app\kernel\common\controller\AppController;
use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\kernel\common\models\exceptions\SaveModelException;
use app\kernel\common\models\exceptions\ValidateException;
use app\kernel\web\http\responses\SuccessResponse;
use app\models\search\UserNotificationSearch;
use app\repositories\UserNotificationActionRepository;
use app\repositories\UserNotificationRepository;
use app\resources\UserNotification\UserNotificationSearchResource;
use app\resources\UserNotification\UserNotificationViewResource;
use app\usecases\Notification\UserNotificationActionService;
use app\usecases\Notification\UserNotificationService;
use yii\base\ErrorException;
use yii\data\ActiveDataProvider;

class UserNotificationController extends AppController
{
	protected UserNotificationRepository       $repository;
	protected UserNotificationService          $service;
	protected UserNotificationActionService    $actionService;
	protected UserNotificationActionRepository $actionRepository;

	public function __construct(
		$id,
		$module,
		UserNotificationRepository $repository,
		UserNotificationService $service,
		UserNotificationActionService $actionService,
		UserNotificationActionRepository $actionRepository,
		array $config = []
	)
	{
		$this->repository       = $repository;
		$this->service          = $service;
		$this->actionService    = $actionService;
		$this->actionRepository = $actionRepository;

		parent::__construct($id, $module, $config);
	}

	/**
	 * @throws ValidateException
	 * @throws ErrorException
	 */
	public function actionIndex(): ActiveDataProvider
	{
		$searchModel = new UserNotificationSearch();

		$dataProvider = $searchModel->search($this->request->get());

		return UserNotificationSearchResource::fromDataProvider($dataProvider);
	}


	/* @throws ModelNotFoundException */
	public function actionView(int $id): UserNotificationViewResource
	{
		$notification = $this->repository->findOneOrThrowWithRelations($id);

		return new UserNotificationViewResource($notification);
	}

	/**
	 * @throws ErrorException
	 */
	public function actionCount(): int
	{
		return $this->repository->countByUserId($this->user->id);
	}

	/**
	 * @throws ModelNotFoundException
	 * @throws SaveModelException
	 */
	public function actionViewed(int $id): UserNotificationViewResource
	{
		$notification = $this->repository->findOneOrThrowWithRelations($id);

		$this->service->viewed($notification);

		return new UserNotificationViewResource($notification);
	}

	/**
	 * @throws ModelNotFoundException
	 * @throws SaveModelException
	 */
	public function actionActed(int $id): UserNotificationViewResource
	{
		$notification = $this->repository->findOneOrThrowWithRelations($id);

		$this->service->acted($notification);

		return new UserNotificationViewResource($notification);
	}

	/**
	 * @throws ModelNotFoundException
	 * @throws SaveModelException
	 */
	public function actionProcessAction(int $id, int $actionId): SuccessResponse
	{
		$action = $this->actionRepository->findOneOrThrow($actionId);

		$this->actionService->process($action, new ProcessUserNotificationActionDto([
			'userId'     => $this->user->id,
			'executedAt' => DateTimeHelper::now()
		]));

		return $this->success();
	}
}
