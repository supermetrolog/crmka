<?php

namespace app\controllers;

use app\kernel\common\models\exceptions\SaveModelException;
use app\kernel\common\models\exceptions\ValidateException;
use app\kernel\common\controller\AppController;
use app\kernel\web\http\responses\SuccessResponse;
use app\models\Equipment;
use app\models\forms\Equipment\EquipmentForm;
use app\models\search\EquipmentSearch;
use app\resources\EquipmentResource;
use app\usecases\Equipment\EquipmentService;
use Exception;
use Throwable;
use yii\data\ActiveDataProvider;
use yii\db\StaleObjectException;
use yii\web\NotFoundHttpException;

class EquipmentController extends AppController
{
	private EquipmentService $service;

	public function __construct(
		$id,
		$module,
		EquipmentService $service,
		array $config = []
	)
	{
		$this->service = $service;

		parent::__construct($id, $module, $config);
	}

	public function actionIndex(): ActiveDataProvider
	{
		$searchModel  = new EquipmentSearch();
		$dataProvider = $searchModel->search($this->request->get());

		return EquipmentResource::fromDataProvider($dataProvider);
	}

	/**
	 * @throws NotFoundHttpException
	 */
	public function actionView(int $id): EquipmentResource
	{
		return new EquipmentResource($this->findModel($id));
	}

	/**
	 * @return EquipmentResource
	 * @throws ValidateException
	 * @throws SaveModelException
	 * @throws Exception
	 */
	public function actionCreate(): EquipmentResource
	{
		$form = new EquipmentForm();

		$form->setScenario(EquipmentForm::SCENARIO_CREATE);

		$form->load($this->request->post());

		$form->validateOrThrow();

		$model = $this->service->create($form->getDto());

		return new EquipmentResource($model);
	}

	/**
	 * @param int $id
	 *
	 * @return EquipmentResource
	 * @throws NotFoundHttpException
	 * @throws SaveModelException
	 * @throws ValidateException
	 * @throws Exception
	 */
	public function actionUpdate(int $id): EquipmentResource
	{
		$model = $this->findModel($id);

		$form = new EquipmentForm();

		$form->setScenario(EquipmentForm::SCENARIO_UPDATE);

		$form->load($this->request->post());
		$form->validateOrThrow();

		$model = $this->service->update($model, $form->getDto());

		return new EquipmentResource($model);
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
	protected function findModel(int $id): ?Equipment
	{
		if (($model = Equipment::findOne($id)) !== null) {
			return $model;
		}

		throw new NotFoundHttpException('The requested page does not exist.');
	}
}
