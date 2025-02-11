<?php

namespace app\controllers;

use app\kernel\common\controller\AppController;
use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\kernel\common\models\exceptions\SaveModelException;
use app\kernel\common\models\exceptions\ValidateException;
use app\kernel\web\http\responses\SuccessResponse;
use app\models\forms\Survey\SurveyForm;
use app\models\forms\SurveyQuestionAnswer\SurveyQuestionAnswerForm;
use app\models\search\SurveySearch;
use app\resources\Survey\SurveyResource;
use app\resources\Survey\SurveyShortResource;
use app\resources\Survey\SurveyWithQuestionsResource;
use app\usecases\Survey\SurveyService;
use Throwable;
use yii\data\ActiveDataProvider;
use yii\db\StaleObjectException;
use yii\web\NotFoundHttpException;

class SurveyController extends AppController
{
	private SurveyService $service;

	public function __construct(
		$id,
		$module,
		SurveyService $service,
		array $config = []
	)
	{
		$this->service = $service;

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
	 * @throws ModelNotFoundException
	 */
	public function actionView(int $id): SurveyResource
	{
		$model = $this->service->getByIdOrThrow($id);

		return new SurveyResource($model);
	}

	/**
	 * @throws ModelNotFoundException
	 */
	public function actionViewWithQuestions(int $id): SurveyWithQuestionsResource
	{
		$survey    = $this->service->getByIdWithRelationsOrThrow($id);
		$questions = $this->service->getQuestionsWithAnswersBySurveyId($id);

		return new SurveyWithQuestionsResource($survey, $questions);
	}

	/**
	 * @return SurveyShortResource
	 * @throws \Exception
	 * @throws ValidateException
	 * @throws SaveModelException
	 */
	public function actionCreate(): SurveyShortResource
	{
		$form = new SurveyForm();

		$form->setScenario(SurveyForm::SCENARIO_CREATE);

		$form->load($this->request->post());

		$form->validateOrThrow();

		$model = $this->service->create($form->getDto());

		return new SurveyShortResource($model);
	}

	/**
	 * @return SurveyShortResource
	 * @throws \Exception
	 * @throws ValidateException
	 * @throws SaveModelException
	 */
	public function actionCreateWithSurveyQuestionAnswer(): SurveyShortResource
	{
		// Create Survey Question Answers

		$answerDtos = [];

		foreach ($this->request->post('question_answers', []) as $questionAnswer) {
			$surveyQuestionAnswerForm = $this->makeQuestionAnswerForm($questionAnswer);
			$answerDtos[]             = $surveyQuestionAnswerForm->getDto();
		}

		// Create Survey

		$surveyForm = new SurveyForm();

		$surveyForm->setScenario(SurveyForm::SCENARIO_CREATE);

		$surveyForm->load($this->request->post());

		$surveyForm->validateOrThrow();

		$model = $this->service->createWithSurveyQuestionAnswer($surveyForm->getDto(), $answerDtos);

		return new SurveyShortResource($model);
	}

	/**
	 * @return SurveyShortResource
	 * @throws \Exception
	 * @throws ModelNotFoundException
	 * @throws SaveModelException
	 * @throws ValidateException
	 */
	public function actionUpdate(int $id): SurveyShortResource
	{
		$model = $this->service->getByIdOrThrow($id);

		$form = new SurveyForm();

		$form->setScenario(SurveyForm::SCENARIO_UPDATE);

		$form->load($this->request->post());
		$form->validateOrThrow();

		$model = $this->service->update($model, $form->getDto());

		return new SurveyShortResource($model);
	}

	/**
	 * @throws \Exception
	 * @throws ModelNotFoundException
	 * @throws SaveModelException
	 * @throws ValidateException|Throwable
	 */
	public function actionUpdateWithSurveyQuestionAnswer(int $id): SurveyWithQuestionsResource
	{
		$survey = $this->service->getByIdOrThrow($id);

		$answerDtos = [];

		foreach ($this->request->post('question_answers', []) as $questionAnswer) {
			$form         = $this->makeQuestionAnswerForm($questionAnswer);
			$answerDtos[] = $form->getDto();
		}

		$survey    = $this->service->updateWithQuestionAnswer($survey, $answerDtos);
		$questions = $this->service->getQuestionsWithAnswersBySurveyId($survey->id);

		return new SurveyWithQuestionsResource($survey, $questions);
	}

	/**
	 * @throws Throwable
	 * @throws StaleObjectException
	 * @throws NotFoundHttpException
	 */
	public function actionDelete(int $id): SuccessResponse
	{
		$model = $this->service->getByIdOrThrow($id);

		$this->service->delete($model);

		return new SuccessResponse();
	}

	/**
	 * @throws ValidateException
	 */
	private function makeQuestionAnswerForm(array $formData): SurveyQuestionAnswerForm
	{
		$form = new SurveyQuestionAnswerForm();

		$form->setScenario(SurveyQuestionAnswerForm::SCENARIO_CREATE_WITH_SURVEY);

		$form->load($formData);

		$form->validateOrThrow();

		return $form;
	}
}
