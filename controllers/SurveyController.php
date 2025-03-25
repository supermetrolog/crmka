<?php

namespace app\controllers;

use app\helpers\ArrayHelper;
use app\helpers\TypeConverterHelper;
use app\kernel\common\controller\AppController;
use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\kernel\common\models\exceptions\SaveModelException;
use app\kernel\common\models\exceptions\ValidateException;
use app\kernel\web\http\responses\ErrorResponse;
use app\kernel\web\http\responses\SuccessResponse;
use app\models\forms\Call\CallForm;
use app\models\forms\Media\MediaForm;
use app\models\forms\Survey\SurveyForm;
use app\models\forms\SurveyQuestionAnswer\SurveyQuestionAnswerForm;
use app\models\Media;
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
use yii\web\UploadedFile;

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
	 * @return SurveyResource|ErrorResponse
	 * @throws Throwable
	 * @throws Exception
	 * @throws ValidateException
	 * @throws SaveModelException
	 */
	public function actionCreateWithSurveyQuestionAnswer()
	{
		// Create Survey

		$surveyForm = new SurveyForm();

		$surveyForm->setScenario(SurveyForm::SCENARIO_CREATE);

		$surveyForm->load($this->request->post());

		$surveyForm->validateOrThrow();

		$surveyDto = $surveyForm->getDto();

		// Calls form

		$callDtos = [];

		foreach ($this->request->post('calls', []) as $call) {
			$callForm = $this->makeCallForm($call);

			$callDtos[] = $callForm->getDto();
		}

		if (ArrayHelper::empty($callDtos) && ArrayHelper::empty($surveyDto->calls)) {
			return $this->error('Нельзя сохранить опрос без звонков.');
		}

		// Create Survey Question Answers

		$answerDtos   = [];
		$mediaDtosMap = [];

		foreach ($this->request->post('question_answers', []) as $key => $questionAnswer) {
			$surveyQuestionAnswerForm = $this->makeQuestionAnswerForm($questionAnswer);
			$answerDto                = $surveyQuestionAnswerForm->getDto();

			if (ArrayHelper::keyExists($questionAnswer, 'file') && TypeConverterHelper::toBool($questionAnswer['file'])) {
				$filesPath = "question_answers[$key][files]";

				$mediaForm = $this->makeMediaForm(Media::CATEGORY_SURVEY_QUESTION_ANSWER, $filesPath);

				$mediaDtosMap[$answerDto->question_answer_id] = $mediaForm->getDtos();
			}

			$answerDtos[] = $answerDto;
		}

		$model = $this->service->createWithSurveyQuestionAnswer($surveyDto, $answerDtos, $callDtos, $mediaDtosMap);

		return new SurveyResource($model);
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

		$answerDtos   = [];
		$mediaDtosMap = [];

		foreach ($this->request->post('question_answers', []) as $key => $questionAnswer) {
			$surveyQuestionAnswerForm = $this->makeQuestionAnswerForm($questionAnswer);
			$answerDto                = $surveyQuestionAnswerForm->getDto();

			if (ArrayHelper::keyExists($questionAnswer, 'file') && TypeConverterHelper::toBool($questionAnswer['file'])) {
				$filesPath = "question_answers[$key][files]";

				$mediaForm = $this->makeMediaForm(Media::CATEGORY_SURVEY_QUESTION_ANSWER, $filesPath);

				$mediaDtosMap[$answerDto->question_answer_id] = $mediaForm->getDtos();
			}

			$answerDtos[] = $answerDto;
		}

		$survey = $this->service->updateWithQuestionAnswer($survey, $answerDtos, $mediaDtosMap);

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

	/**
	 * @throws ValidateException
	 */
	private function makeMediaForm(string $category, string $name): MediaForm
	{
		$form = new MediaForm();

		$form->category   = $category;
		$form->model_id   = $this->user->id;
		$form->model_type = $this->user->identity::getMorphClass();

		$form->files = UploadedFile::getInstancesByName($name);

		$form->validateOrThrow();

		return $form;
	}

	/**
	 * @throws ValidateException
	 */
	private function makeCallForm(array $formData, string $scenario = CallForm::SCENARIO_CREATE): CallForm
	{
		$callForm = new CallForm();

		$callForm->setScenario($scenario);
		$callForm->load($formData);

		$callForm->validateOrThrow();

		return $callForm;
	}

}
