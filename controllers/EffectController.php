<?php

namespace app\controllers;

use app\kernel\common\controller\AppController;
use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\kernel\common\models\exceptions\SaveModelException;
use app\kernel\common\models\exceptions\ValidateException;
use app\kernel\web\http\responses\SuccessResponse;
use app\models\Effect;
use app\models\forms\Effect\EffectForm;
use app\models\search\EffectSearch;
use app\resources\EffectResource;
use app\usecases\Effect\EffectService;
use Throwable;
use yii\base\ErrorException;
use yii\data\ActiveDataProvider;
use yii\db\StaleObjectException;

class EffectController extends AppController
{
	private EffectService $effectService;

	public function __construct(
		$id,
		$module,
		EffectService $effectService,
		array $config = []
	)
	{
		$this->effectService = $effectService;

		parent::__construct($id, $module, $config);
	}

	/**
	 * @throws ValidateException
	 * @throws ErrorException
	 */
	public function actionIndex(): ActiveDataProvider
	{
		$searchModel  = new EffectSearch();
		$dataProvider = $searchModel->search($this->request->get());

		return EffectResource::fromDataProvider($dataProvider);
	}

	/**
	 * @throws ModelNotFoundException
	 */
	public function actionView(int $id): EffectResource
	{
		return new EffectResource($this->findModel($id));
	}

	/**
	 * @throws ValidateException
	 * @throws SaveModelException
	 */
	public function actionCreate(): EffectResource
	{
		$form = new EffectForm();

		$form->setScenario(EffectForm::SCENARIO_CREATE);

		$form->load($this->request->post());

		$form->validateOrThrow();

		$model = $this->effectService->create($form->getDto());

		return new EffectResource($model);
	}

	/**
	 * @throws SaveModelException
	 * @throws ValidateException
	 * @throws ModelNotFoundException
	 */
	public function actionUpdate(int $id): EffectResource
	{
		$model = $this->findModel($id);

		$form = new EffectForm();

		$form->setScenario(EffectForm::SCENARIO_UPDATE);

		$form->load($this->request->post());
		$form->validateOrThrow();

		$model = $this->effectService->update($model, $form->getDto());

		return new EffectResource($model);
	}

	/**
	 * @throws Throwable
	 * @throws StaleObjectException
	 * @throws ModelNotFoundException
	 */
	public function actionDelete(int $id): SuccessResponse
	{
		$this->effectService->delete($this->findModel($id));

		return new SuccessResponse();
	}

	/**
	 * @throws ModelNotFoundException
	 */
	protected function findModel(int $id): Effect
	{
		return Effect::find()->byId($id)->oneOrThrow();
	}
}
