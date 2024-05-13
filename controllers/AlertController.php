<?php

namespace app\controllers;

use app\kernel\common\controller\AppController;
use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\kernel\common\models\exceptions\SaveModelException;
use app\kernel\common\models\exceptions\ValidateException;
use app\kernel\web\http\responses\SuccessResponse;
use app\models\Alert;
use app\models\forms\Alert\AlertForm;
use app\models\search\AlertSearch;
use app\repositories\AlertRepository;
use app\resources\AlertResource;
use app\usecases\Alert\AlertService;
use app\usecases\Alert\CreateAlertService;
use Exception;
use Throwable;
use Yii;
use yii\base\ErrorException;
use yii\data\ActiveDataProvider;
use yii\db\StaleObjectException;
use yii\web\NotFoundHttpException;

class AlertController extends AppController
{
	private AlertService       $service;
	private CreateAlertService $createAlertService;
	private AlertRepository    $repository;


	public function __construct(
		$id,
		$module,
		AlertService $service,
		CreateAlertService $createAlertService,
		AlertRepository $repository,
		array $config = []
	)
	{
		$this->service            = $service;
		$this->createAlertService = $createAlertService;
		$this->repository         = $repository;

		parent::__construct($id, $module, $config);
	}

	/**
	 * @return ActiveDataProvider
	 * @throws ValidateException
	 */
	public function actionIndex(): ActiveDataProvider
	{
		$searchModel  = new AlertSearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		return AlertResource::fromDataProvider($dataProvider);
	}


	/**
	 * @throws ModelNotFoundException
	 */
	public function actionView(int $id): AlertResource
	{
		return new AlertResource($this->findModelByIdAndCreatedBy($id));
	}

	/**
	 * @throws SaveModelException
	 * @throws ValidateException
	 * @throws Exception
	 */
	public function actionCreate(): AlertResource
	{
		$form = new AlertForm();

		$form->setScenario(AlertForm::SCENARIO_CREATE);

		$form->load($this->request->post());

		$form->created_by_id   = $this->user->id;
		$form->created_by_type = $this->user->identity::getMorphClass();

		$form->validateOrThrow();

		$model = $this->createAlertService->create($form->getDto());

		return new AlertResource($model);
	}

	/**
	 * @throws ErrorException
	 * @throws SaveModelException
	 * @throws Throwable
	 * @throws ValidateException
	 */
	public function actionCreateForUsers(): array
	{
		$form = new AlertForm();

		$form->setScenario(AlertForm::SCENARIO_CREATE_FOR_USERS);

		$form->load($this->request->post());

		$form->created_by_id   = $this->user->id;
		$form->created_by_type = $this->user->identity::getMorphClass();

		$form->validateOrThrow();

		$models = $this->createAlertService->createForUsers($form->getDto());

		return AlertResource::collection($models);
	}

	/**
	 * @throws ModelNotFoundException
	 * @throws SaveModelException
	 * @throws ValidateException
	 */
	public function actionUpdate(int $id): AlertResource
	{
		$model = $this->findModelByIdAndCreatedByOrUserId($id);

		$form = new AlertForm();

		$form->setScenario(AlertForm::SCENARIO_UPDATE);

		$form->load($this->request->post());
		$form->validateOrThrow();

		$model = $this->service->update($model, $form->getDto());

		return new AlertResource($model);
	}

	/**
	 * @throws Throwable
	 * @throws StaleObjectException
	 * @throws NotFoundHttpException
	 */
	public function actionDelete(int $id): SuccessResponse
	{
		$this->service->delete($this->findModelByIdAndCreatedBy($id));

		return new SuccessResponse();
	}


	/**
	 * @throws ModelNotFoundException
	 */
	protected function findModelByIdAndCreatedBy(int $id): Alert
	{
		return $this->repository->findModelByIdAndCreatedBy($id, $this->user->id, $this->user->identity::getMorphClass());
	}

	/**
	 * @throws ModelNotFoundException
	 */
	protected function findModelByIdAndCreatedByOrUserId(int $id): Alert
	{
		return $this->repository->findModelByIdAndCreatedByOrUserId($id, $this->user->id, $this->user->identity::getMorphClass());
	}
}
