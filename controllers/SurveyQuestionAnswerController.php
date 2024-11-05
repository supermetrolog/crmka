<?php

namespace app\controllers;

use app\kernel\common\controller\AppController;
use app\kernel\common\models\exceptions\SaveModelException;
use app\kernel\common\models\exceptions\ValidateException;
use app\kernel\web\http\responses\SuccessResponse;
use app\models\forms\SurveyQuestionAnswer\SurveyQuestionAnswerForm;
use app\models\search\SurveyQuestionAnswerSearch;
use app\models\SurveyQuestionAnswer;
use app\repositories\SurveyQuestionAnswerRepository;
use app\resources\Survey\SurveyQuestionAnswerResource;
use app\usecases\SurveyQuestionAnswer\SurveyQuestionAnswerService;
use Throwable;
use yii\data\ActiveDataProvider;
use yii\db\StaleObjectException;
use yii\web\NotFoundHttpException;

class SurveyQuestionAnswerController extends AppController
{
	private SurveyQuestionAnswerService    $service;
	private SurveyQuestionAnswerRepository $repository;

	public function __construct(
		$id,
		$module,
		SurveyQuestionAnswerService $service,
		SurveyQuestionAnswerRepository $repository,
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
		$searchModel  = new SurveyQuestionAnswerSearch();
		$dataProvider = $searchModel->search($this->request->get());

		return SurveyQuestionAnswerResource::fromDataProvider($dataProvider);
	}

	/**
	 * @throws NotFoundHttpException
	 */
	public function actionView(int $id): SurveyQuestionAnswerResource
	{
		return new SurveyQuestionAnswerResource($this->findModel($id));
	}

	/**
	 * @return SurveyQuestionAnswerResource
	 * @throws ValidateException
	 * @throws SaveModelException
	 */
	public function actionCreate(): SurveyQuestionAnswerResource
	{
		$form = new SurveyQuestionAnswerForm();

		$form->setScenario(SurveyQuestionAnswerForm::SCENARIO_CREATE);

		$form->load($this->request->post());

		$form->validateOrThrow();

		$model = $this->service->create($form->getDto());

		return new SurveyQuestionAnswerResource($model);
	}

	/**
	 * @param int $id
	 *
	 * @return SurveyQuestionAnswerResource
	 * @throws NotFoundHttpException
	 * @throws SaveModelException
	 * @throws ValidateException
	 */
	public function actionUpdate(int $id): SurveyQuestionAnswerResource
	{
		$model = $this->findModel($id);

		$form = new SurveyQuestionAnswerForm();

		$form->setScenario(SurveyQuestionAnswerForm::SCENARIO_UPDATE);

		$form->load($this->request->post());
		$form->validateOrThrow();

		$model = $this->service->update($model, $form->getDto());

		return new SurveyQuestionAnswerResource($model);
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
	protected function findModel(int $id): ?SurveyQuestionAnswer
	{
		if (($model = SurveyQuestionAnswer::findOne($id)) !== null) {
			return $model;
		}

		throw new NotFoundHttpException('The requested page does not exist.');
	}
}
