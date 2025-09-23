<?php

namespace app\controllers;

use app\dto\UserNotification\ProcessUserNotificationActionDto;
use app\dto\UserNotification\UserNotificationActionDto;
use app\helpers\DateTimeHelper;
use app\kernel\common\controller\AppController;
use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\kernel\common\models\exceptions\SaveModelException;
use app\kernel\common\models\exceptions\ValidateException;
use app\kernel\web\http\responses\SuccessResponse;
use app\models\forms\User\UserNotificationActionSendForm;
use app\models\forms\User\UserNotificationSendForm;
use app\models\search\UserNotificationSearch;
use app\repositories\UserNotificationActionRepository;
use app\repositories\UserNotificationRepository;
use app\resources\UserNotification\UserNotificationSearchResource;
use app\resources\UserNotification\UserNotificationViewResource;
use app\usecases\Notification\SendUserNotificationService;
use app\usecases\Notification\UserNotificationActionService;
use app\usecases\Notification\UserNotificationService;
use Exception;
use yii\base\ErrorException;
use yii\data\ActiveDataProvider;
use yii\web\ForbiddenHttpException;

class UserNotificationController extends AppController
{
	protected UserNotificationRepository       $repository;
	protected UserNotificationService          $service;
	protected UserNotificationActionService    $actionService;
	protected UserNotificationActionRepository $actionRepository;
	protected SendUserNotificationService      $sendService;

	public function __construct(
		$id,
		$module,
		UserNotificationRepository $repository,
		UserNotificationService $service,
		UserNotificationActionService $actionService,
		UserNotificationActionRepository $actionRepository,
		SendUserNotificationService $sendService,
		array $config = []
	)
	{
		$this->repository       = $repository;
		$this->service          = $service;
		$this->actionService    = $actionService;
		$this->actionRepository = $actionRepository;
		$this->sendService      = $sendService;

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
		return $this->repository->countByUserId((int)$this->user->id);
	}

	/**
	 * @throws ModelNotFoundException
	 * @throws SaveModelException
	 * @throws ForbiddenHttpException
	 */
	public function actionViewed(int $id): UserNotificationViewResource
	{
		$notification = $this->repository->findOneOrThrowWithRelations($id);

		if ($this->user->id !== $notification->user_id) {
			throw new ForbiddenHttpException('Нет доступа');
		}

		$this->service->viewed($notification);

		return new UserNotificationViewResource($notification);
	}

	/**
	 * @throws ModelNotFoundException
	 * @throws SaveModelException
	 * @throws ForbiddenHttpException
	 */
	public function actionActed(int $id): UserNotificationViewResource
	{
		$notification = $this->repository->findOneOrThrowWithRelations($id);

		if ($this->user->id !== $notification->user_id) {
			throw new ForbiddenHttpException('Нет доступа');
		}

		$this->service->acted($notification);

		return new UserNotificationViewResource($notification);
	}

	/**
	 * @throws ErrorException
	 */
	public function actionActedAll(): SuccessResponse
	{
		$processedCount = $this->service->actedAllForUser($this->user->identity);

		return $this->successf('Обработано %d уведомлений', [$processedCount]);
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

	/**
	 * @throws ValidateException
	 * @throws Exception
	 */
	public function actionSend(): SuccessResponse
	{
		$dtos = [];

		foreach ($this->request->post('user_ids', []) as $userId) {
			$form = new UserNotificationSendForm();

			$form->load($this->request->post());

			$form->user_id = (int)$userId;

			$form->validateOrThrow();

			$dto = $form->getDto();

			$dto->createdByType = $this->user->identity::getMorphClass();
			$dto->createdById   = $this->user->identity->id;

			$dtos[] = $dto;
		}

		$actionDtos = [];

		foreach ($this->request->post('actions', []) as $action) {
			$actionDtos[] = $this->makeActionDto($action);
		}

		$this->sendService->sendAll($dtos, $actionDtos);

		return $this->success('Уведомление отправлено');
	}

	/**
	 * @throws ValidateException
	 * @throws Exception
	 */
	private function makeActionDto(array $payload): UserNotificationActionDto
	{
		$form = new UserNotificationActionSendForm();

		$form->load($payload);

		$form->validateOrThrow();

		return $form->getDto();
	}
}
