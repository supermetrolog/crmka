<?php

namespace app\controllers;

use app\kernel\common\models\exceptions\SaveModelException;
use app\kernel\common\models\exceptions\ValidateException;
use app\kernel\common\controller\AppController;
use app\kernel\web\http\responses\SuccessResponse;
use app\models\forms\Field\FieldForm;
use app\models\search\FieldSearch;
use app\models\Field;
use app\repositories\FieldRepository;
use app\resources\FieldResource;
use app\usecases\Field\FieldService;
use Throwable;
use yii\data\ActiveDataProvider;
use yii\db\StaleObjectException;
use yii\web\NotFoundHttpException;

class FieldController extends AppController
{
	private FieldService    $service;
	private FieldRepository $repository;

	public function __construct(
		$id,
		$module,
		FieldService $service,
		FieldRepository $repository,
		array $config = []
	)
	{
		$this->service    = $service;
		$this->repository = $repository;

		parent::__construct($id, $module, $config);
	}

	/**
	 * @throws ValidateException
	 */
	public function actionIndex(): ActiveDataProvider
	{
		$searchModel  = new FieldSearch();
		$dataProvider = $searchModel->search($this->request->get());

		return FieldResource::fromDataProvider($dataProvider);
	}

	/**
	 * @throws NotFoundHttpException
	 */
	public function actionView(int $id): FieldResource
	{
		return new FieldResource($this->findModel($id));
	}

	/**
	 * @return FieldResource
	 * @throws ValidateException
	 * @throws SaveModelException
	 */
	public function actionCreate(): FieldResource
	{
		$form = new FieldForm();

		$form->setScenario(FieldForm::SCENARIO_CREATE);

		$form->load($this->request->post());

		$form->validateOrThrow();

		$model = $this->service->create($form->getDto());

		return new FieldResource($model);
	}

	/**
	 * @param int $id
	 *
	 * @return FieldResource
	 * @throws NotFoundHttpException
	 * @throws SaveModelException
	 * @throws ValidateException
	 */
	public function actionUpdate(int $id): FieldResource
	{
		$model = $this->findModel($id);

		$form = new FieldForm();

		$form->setScenario(FieldForm::SCENARIO_UPDATE);

		$form->load($this->request->post());
		$form->validateOrThrow();

		$model = $this->service->update($model, $form->getDto());

		return new FieldResource($model);
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
	protected function findModel(int $id): ?Field
	{
		if (($model = Field::findOne($id)) !== null) {
			return $model;
		}

		throw new NotFoundHttpException('The requested page does not exist.');
	}
}
