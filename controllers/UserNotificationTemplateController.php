<?php

namespace app\controllers;

use app\kernel\common\controller\AppController;
use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\kernel\common\models\exceptions\SaveModelException;
use app\kernel\common\models\exceptions\ValidateException;
use app\kernel\web\http\responses\SuccessResponse;
use app\models\forms\Notification\UserNotificationTemplateForm;
use app\models\search\UserNotificationTemplateSearch;
use app\repositories\UserNotificationTemplateRepository;
use app\resources\UserNotification\UserNotificationTemplateViewResource;
use app\usecases\Notification\UserNotificationTemplateService;
use Throwable;
use yii\base\ErrorException;
use yii\data\ActiveDataProvider;
use yii\db\StaleObjectException;

class UserNotificationTemplateController extends AppController
{
	protected UserNotificationTemplateService    $service;
	protected UserNotificationTemplateRepository $repository;

	public function __construct(
		$id,
		$module,
		UserNotificationTemplateService $service,
		UserNotificationTemplateRepository $repository,
		array $config = []
	)
	{
		$this->service    = $service;
		$this->repository = $repository;

		parent::__construct($id, $module, $config);
	}

	/**
	 * @throws ValidateException
	 * @throws ErrorException
	 */
	public function actionIndex(): ActiveDataProvider
	{
		$searchModel = new UserNotificationTemplateSearch();

		$dataProvider = $searchModel->search($this->request->get());

		return UserNotificationTemplateViewResource::fromDataProvider($dataProvider);
	}

	/**
	 * @throws ModelNotFoundException
	 */
	public function actionView(int $id): UserNotificationTemplateViewResource
	{
		$notification = $this->repository->findOneOrThrow($id);

		return new UserNotificationTemplateViewResource($notification);
	}

	/**
	 * @throws ValidateException
	 * @throws SaveModelException
	 */
	public function actionCreate(): UserNotificationTemplateViewResource
	{
		$form = new UserNotificationTemplateForm();

		$form->setScenario(UserNotificationTemplateForm::SCENARIO_CREATE);

		$form->load($this->request->post());

		$form->validateOrThrow();

		$model = $this->service->create($form->getDto());

		return new UserNotificationTemplateViewResource($model);
	}

	/**
	 * @throws SaveModelException
	 * @throws ValidateException
	 * @throws ModelNotFoundException
	 */
	public function actionUpdate(int $id): UserNotificationTemplateViewResource
	{
		$template = $this->repository->findOneOrThrow($id);

		$form = new UserNotificationTemplateForm();

		$form->setScenario(UserNotificationTemplateForm::SCENARIO_UPDATE);

		$form->load($this->request->post());

		$form->validateOrThrow();

		$model = $this->service->update($template, $form->getDto());

		return new UserNotificationTemplateViewResource($model);
	}

	/**
	 * @throws StaleObjectException
	 * @throws ModelNotFoundException
	 * @throws Throwable
	 */
	public function actionDelete(int $id): SuccessResponse
	{
		$this->service->delete($this->repository->findOneOrThrow($id));

		return $this->success();
	}

	/**
	 * @throws SaveModelException
	 * @throws ModelNotFoundException
	 */
	public function actionDisable(int $id): UserNotificationTemplateViewResource
	{
		$template = $this->repository->findOneOrThrow($id);

		$this->service->disable($template);

		return new UserNotificationTemplateViewResource($template);
	}

	/**
	 * @throws SaveModelException
	 * @throws ModelNotFoundException
	 */
	public function actionEnable(int $id): UserNotificationTemplateViewResource
	{
		$template = $this->repository->findOneOrThrow($id);

		$this->service->enable($template);

		return new UserNotificationTemplateViewResource($template);
	}
}
