<?php

namespace app\controllers;

use app\kernel\common\controller\AppController;
use app\kernel\common\models\exceptions\SaveModelException;
use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\kernel\common\models\exceptions\ValidateException;
use app\kernel\web\http\responses\SuccessResponse;
use app\models\forms\Call\CallForm;
use app\models\search\CallSearch;
use app\models\Call;
use app\repositories\CallRepository;
use app\resources\CallResource;
use app\usecases\Call\CallService;
use app\usecases\Call\CreateCallService;
use Throwable;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\StaleObjectException;
use yii\web\NotFoundHttpException;

class CallController extends AppController
{
	private CallService       $service;
	private CreateCallService $createCallService;
	private CallRepository    $repository;


	public function __construct(
		$id,
		$module,
		CallService $service,
		CreateCallService $createCallService,
		CallRepository $repository,
		array $config = []
	)
	{
		$this->service           = $service;
		$this->createCallService = $createCallService;
		$this->repository        = $repository;

		parent::__construct($id, $module, $config);
	}

	/**
	 * @throws ValidateException
	 */
	public function actionIndex(): ActiveDataProvider
	{
		$searchModel  = new CallSearch();
		$dataProvider = $searchModel->search($this->request->get());

		return CallResource::fromDataProvider($dataProvider);
	}

	/**
	 * @throws NotFoundHttpException
	 */
	public function actionView(int $id): CallResource
	{
		return new CallResource($this->findModel($id));
	}

	/**
	 * @return CallResource
	 * @throws ValidateException
	 * @throws SaveModelException
	 */
	public function actionCreate(): CallResource
	{
		$form = new CallForm();

		$form->setScenario(CallForm::SCENARIO_CREATE);

		$form->load($this->request->post());

		$form->validateOrThrow();

		$model = $this->createCallService->create($form->getDto());

		return new CallResource($model);
	}

	/**
	 * @param int $id
	 *
	 * @return CallResource
	 * @throws NotFoundHttpException
	 * @throws SaveModelException
	 * @throws ValidateException
	 */
	public function actionUpdate(int $id): CallResource
	{
		$model = $this->findModel($id);

		$form = new CallForm();

		$form->setScenario(CallForm::SCENARIO_UPDATE);

		$form->load($this->request->post());
		$form->validateOrThrow();

		$model = $this->service->update($model, $form->getDto());

		return new CallResource($model);
	}

	/**
	 * @throws Throwable
	 * @throws StaleObjectException
	 * @throws NotFoundHttpException
	 */
	public function actionDelete(int $id): SuccessResponse
	{
		$this->service->delete($this->findModel($id));

		return new SuccessResponse();
	}

	/**
	 * @throws NotFoundHttpException
	 */
	protected function findModel(int $id): ?Call
	{
		if (($model = Call::findOne($id)) !== null) {
			return $model;
		}

		throw new NotFoundHttpException('The requested page does not exist.');
	}
}
