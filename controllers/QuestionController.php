<?php

namespace app\controllers;

use app\kernel\common\models\exceptions\SaveModelException;
use app\kernel\common\models\exceptions\ValidateException;
use app\kernel\common\controller\AppController;
use app\kernel\web\http\responses\SuccessResponse;
use app\models\forms\Question\QuestionForm;
use app\models\forms\QuestionAnswer\QuestionAnswerForm;
use app\models\search\QuestionSearch;
use app\models\Question;
use app\repositories\QuestionRepository;
use app\resources\QuestionResource;
use app\resources\QuestionWithQuestionAnswerResource;
use app\resources\QuestionWithQuestionAnswersResource;
use app\usecases\Question\QuestionService;
use Throwable;
use yii\data\ActiveDataProvider;
use yii\db\StaleObjectException;
use yii\web\NotFoundHttpException;

class QuestionController extends AppController
{
	private QuestionService    $service;
	private QuestionRepository $repository;

	public function __construct(
		$id,
		$module,
		QuestionService $service,
		QuestionRepository $repository,
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
		$searchModel  = new QuestionSearch();
		$dataProvider = $searchModel->search($this->request->get());

		return QuestionResource::fromDataProvider($dataProvider);
	}

	/**
	 * @throws ValidateException
	 */
	public function actionIndexWithQuestionAnswer(): ActiveDataProvider
	{
		$searchModel  = new QuestionSearch();
		$dataProvider = $searchModel->search($this->request->get());

		return QuestionWithQuestionAnswerResource::fromDataProvider($dataProvider);
	}

	/**
	 * @throws NotFoundHttpException
	 */
	public function actionView(int $id): QuestionResource
	{
		return new QuestionResource($this->findModel($id));
	}

	/**
	 * @return QuestionResource
	 * @throws ValidateException
	 * @throws SaveModelException
	 */
	public function actionCreate(): QuestionResource
	{
		$form = new QuestionForm();

		$form->setScenario(QuestionForm::SCENARIO_CREATE);

		$form->load($this->request->post());

		$form->validateOrThrow();

		$model = $this->service->create($form->getDto());

		return new QuestionResource($model);
	}

	/**
	 * @return QuestionResource
	 * @throws ValidateException
	 * @throws SaveModelException
	 */
	public function actionCreateWithQuestionAnswer(): QuestionResource
	{
		// Create Question Answer

		$answerForm = new QuestionAnswerForm();

		$answerForm->setScenario(QuestionAnswerForm::SCENARIO_CREATE_WITH_QUESTION);

		$answerForm->load($this->request->post());

		$answerForm->validateOrThrow();

		// Create Question

		$questionForm = new QuestionForm();

		$questionForm->setScenario(QuestionForm::SCENARIO_CREATE);

		$questionForm->load($this->request->post());

		$questionForm->validateOrThrow();

		$model = $this->service->createWithQuestionAnswer($questionForm->getDto(), $answerForm->getDto());

		return new QuestionResource($model);
	}

	/**
	 * @param int $id
	 *
	 * @return QuestionResource
	 * @throws NotFoundHttpException
	 * @throws SaveModelException
	 * @throws ValidateException
	 */
	public function actionUpdate(int $id): QuestionResource
	{
		$model = $this->findModel($id);

		$form = new QuestionForm();

		$form->setScenario(QuestionForm::SCENARIO_UPDATE);

		$form->load($this->request->post());
		$form->validateOrThrow();

		$model = $this->service->update($model, $form->getDto());

		return new QuestionResource($model);
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
	protected function findModel(int $id): ?Question
	{
		if (($model = Question::findOne($id)) !== null) {
			return $model;
		}

		throw new NotFoundHttpException('The requested page does not exist.');
	}
}
