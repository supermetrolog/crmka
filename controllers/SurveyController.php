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
use app\repositories\QuestionRepository;
use app\repositories\SurveyRepository;
use app\resources\Survey\SurveyResource;
use app\resources\Survey\SurveyShortResource;
use app\resources\Survey\SurveyWithQuestionsResource;
use app\usecases\Survey\SurveyService;
use Exception;
use Throwable;
use yii\data\ActiveDataProvider;
use yii\db\StaleObjectException;

class SurveyController extends AppController
{
	private SurveyService      $service;
	private SurveyRepository   $repository;
	private QuestionRepository $questionRepository;

	public function __construct(
		$id,
		$module,
		SurveyService $service,
		SurveyRepository $repository,
		QuestionRepository $questionRepository,
		array $config = []
	)
	{
		$this->service            = $service;
		$this->repository         = $repository;
		$this->questionRepository = $questionRepository;

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
		$model = $this->repository->findOneOrThrow($id);

		return new SurveyResource($model);
	}

	/**
	 * @throws ModelNotFoundException
	 */
	public function actionViewWithQuestions(int $id): SurveyWithQuestionsResource
	{
		$survey    = $this->repository->findOneByIdWithRelationsOrThrow($id);
		$questions = $this->questionRepository->findAllBySurveyIdWithAnswers($id);

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
	 * @throws Throwable
	 * @throws Exception
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
	 * @throws ModelNotFoundException
	 * @throws SaveModelException
	 * @throws ValidateException
	 * @throws Exception
	 */
	public function actionUpdate(int $id): SurveyShortResource
	{
		$model = $this->repository->findOneOrThrow($id);

		$form = new SurveyForm();

		$form->setScenario(SurveyForm::SCENARIO_UPDATE);

		$form->load($this->request->post());
		$form->validateOrThrow();

		$model = $this->service->update($model, $form->getDto());

		return new SurveyShortResource($model);
	}

	/**
	 * @throws ModelNotFoundException
	 * @throws SaveModelException
	 * @throws Throwable
	 * @throws ValidateException
	 */
	public function actionUpdateWithSurveyQuestionAnswer(int $id): SurveyWithQuestionsResource
	{
		$survey = $this->repository->findOneOrThrow($id);

		$answerDtos = [];

		foreach ($this->request->post('question_answers', []) as $questionAnswer) {
			$form         = $this->makeQuestionAnswerForm($questionAnswer);
			$answerDtos[] = $form->getDto();
		}

		$survey    = $this->service->updateWithQuestionAnswer($survey, $answerDtos);
		$questions = $this->questionRepository->findAllBySurveyIdWithAnswers($survey->id);

		return new SurveyWithQuestionsResource($survey, $questions);
	}

	/**
	 * @throws ModelNotFoundException
	 * @throws StaleObjectException
	 * @throws Throwable
	 */
	public function actionDelete(int $id): SuccessResponse
	{
		$model = $this->repository->findOneOrThrow($id);

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
