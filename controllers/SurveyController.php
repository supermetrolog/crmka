<?php

namespace app\controllers;

use app\kernel\common\controller\AppController;
use app\kernel\common\models\exceptions\SaveModelException;
use app\kernel\common\models\exceptions\ValidateException;
use app\kernel\web\http\responses\SuccessResponse;
use app\models\forms\Survey\SurveyForm;
use app\models\forms\SurveyQuestionAnswer\SurveyQuestionAnswerForm;
use app\models\Question;
use app\models\search\SurveySearch;
use app\models\Survey;
use app\models\SurveyQuestionAnswer;
use app\repositories\SurveyRepository;
use app\resources\Survey\SurveyResource;
use app\resources\Survey\SurveyShortResource;
use app\resources\Survey\SurveyWithQuestionsResource;
use app\usecases\Survey\SurveyService;
use Throwable;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
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
	 * @throws NotFoundHttpException
	 */
	public function actionViewWithQuestions(int $id): SurveyWithQuestionsResource
	{
		return new SurveyWithQuestionsResource(
			$this->findModel($id),
			Question::find()
			        ->joinWith([
				        'answers.surveyQuestionAnswer' => function (ActiveQuery $query) use ($id) {
					        $query->where([SurveyQuestionAnswer::field('survey_id') => $id]);
					        $query->with(['tasks.user.userProfile',
					                      'tasks.tags',
					                      'tasks.createdByUser.userProfile',
					                      'tasks.observers.user.userProfile',
					                      'tasks.targetUserObserver']);
				        }
			        ])
			        ->all(),
		);
	}

	/**
	 * @return SurveyShortResource
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
	 * @throws ValidateException
	 * @throws SaveModelException
	 */
	public function actionCreateWithSurveyQuestionAnswer(): SurveyShortResource
	{
		// Create Survey Question Answers

		$answerDtos = [];

		foreach ($this->request->post('question_answers') ?? [] as $questionAnswer) {
			$surveyQuestionAnswerForm = new SurveyQuestionAnswerForm();

			$surveyQuestionAnswerForm->setScenario(SurveyQuestionAnswerForm::SCENARIO_CREATE_WITH_SURVEY);

			$surveyQuestionAnswerForm->load($questionAnswer);

			$surveyQuestionAnswerForm->validateOrThrow();

			$answerDtos[] = $surveyQuestionAnswerForm->getDto();
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
	 * @param int $id
	 *
	 * @return SurveyShortResource
	 * @throws NotFoundHttpException
	 * @throws SaveModelException
	 * @throws ValidateException
	 */
	public function actionUpdate(int $id): SurveyShortResource
	{
		$model = $this->findModel($id);

		$form = new SurveyForm();

		$form->setScenario(SurveyForm::SCENARIO_UPDATE);

		$form->load($this->request->post());
		$form->validateOrThrow();

		$model = $this->service->update($model, $form->getDto());

		return new SurveyShortResource($model);
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
