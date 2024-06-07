<?php

namespace app\controllers;

use app\kernel\common\models\exceptions\SaveModelException;
use app\kernel\common\models\exceptions\ValidateException;
use app\kernel\common\controller\AppController;
use app\kernel\web\http\responses\SuccessResponse;
use app\models\forms\Survey\SurveyForm;
use app\models\forms\SurveyQuestionAnswer\SurveyQuestionAnswerForm;
use app\models\search\SurveySearch;
use app\models\Survey;
use app\repositories\SurveyRepository;
use app\resources\SurveyResource;
use app\usecases\Survey\SurveyService;
use Throwable;
use yii\data\ActiveDataProvider;
use yii\db\StaleObjectException;
use yii\web\NotFoundHttpException;

class SurveyController extends AppController
{
	private SurveyService    $service;
	private SurveyRepository $repository;
	
	public function __construct(
		$id,
		$module,
		SurveyService $service,
		SurveyRepository $repository,
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
		$searchModel  = new SurveySearch();
		$dataProvider = $searchModel->search($this->request->get());

		return SurveyResource::fromDataProvider($dataProvider);
	}

	/**
	 * @throws NotFoundHttpException
	 */
	public function actionView(int $id): SurveyResource
	{
		return new SurveyResource($this->findModel($id));
	}

	/**
	 * @return SurveyResource
	 * @throws ValidateException
	 * @throws SaveModelException
	 */
	public function actionCreate(): SurveyResource
	{
		$form = new SurveyForm();

		$form->setScenario(SurveyForm::SCENARIO_CREATE);

		$form->load($this->request->post());

		$form->validateOrThrow();

		$model = $this->service->create($form->getDto());

		return new SurveyResource($model);
	}

	/**
	 * @return SurveyResource
	 * @throws ValidateException
	 * @throws SaveModelException
	 */
	public function actionCreateWithSurveyQuestionAnswer(): SurveyResource
	{
		// Create Survey Question Answer

		$surveyQuestionAnswerForm = new SurveyQuestionAnswerForm();

		$surveyQuestionAnswerForm->setScenario(SurveyQuestionAnswerForm::SCENARIO_CREATE);

		$surveyQuestionAnswerForm->load($this->request->post());

		$surveyQuestionAnswerForm->validateOrThrow();

		// Create Survey

		$surveyForm = new SurveyForm();

		$surveyForm->setScenario(SurveyForm::SCENARIO_CREATE);

		$surveyForm->load($this->request->post());

		$surveyForm->validateOrThrow();

		$model = $this->service->createWithSurveyQuestionAnswer($surveyForm->getDto(), $surveyQuestionAnswerForm->getDto());

		return new SurveyResource($model);
	}

	/**
	 * @param int $id
	 *
	 * @return SurveyResource
	 * @throws NotFoundHttpException
	 * @throws SaveModelException
	 * @throws ValidateException
	 */
	public function actionUpdate(int $id): SurveyResource
	{
		$model = $this->findModel($id);

		$form = new SurveyForm();

		$form->setScenario(SurveyForm::SCENARIO_UPDATE);

		$form->load($this->request->post());
		$form->validateOrThrow();

		$model = $this->service->update($model, $form->getDto());

		return new SurveyResource($model);
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
	protected function findModel(int $id): ?Survey
	{
		if (($model = Survey::findOne($id)) !== null) {
			return $model;
		}

		throw new NotFoundHttpException('The requested page does not exist.');
	}
}
