<?php

namespace app\controllers;

use app\kernel\common\controller\AppController;
use app\kernel\common\models\exceptions\SaveModelException;
use app\kernel\common\models\exceptions\ValidateException;
use app\kernel\web\http\responses\SuccessResponse;
use app\models\forms\QuestionAnswer\QuestionAnswerForm;
use app\models\QuestionAnswer;
use app\models\search\QuestionAnswerSearch;
use app\repositories\QuestionAnswerRepository;
use app\resources\QuestionAnswerResource;
use app\resources\QuestionAnswerWithQuestionResource;
use app\usecases\QuestionAnswer\QuestionAnswerService;
use Throwable;
use yii\base\ErrorException;
use yii\data\ActiveDataProvider;
use yii\db\Exception;
use yii\db\StaleObjectException;
use yii\web\NotFoundHttpException;

class QuestionAnswerController extends AppController
{
	private QuestionAnswerService    $service;
	private QuestionAnswerRepository $repository;

	public function __construct(
		$id,
		$module,
		QuestionAnswerService $service,
		QuestionAnswerRepository $repository,
		array $config = []
	)
	{
		$this->service    = $service;
		$this->repository = $repository;

		parent::__construct($id, $module, $config);
	}

	/**
	 * @throws ValidateException
	 * @throws ErrorException
	 */
	public function actionIndex(): ActiveDataProvider
	{
		$searchModel  = new QuestionAnswerSearch();
		$dataProvider = $searchModel->search($this->request->get());

		return QuestionAnswerResource::fromDataProvider($dataProvider);
	}

	/**
	 * @throws ValidateException
	 * @throws ErrorException
	 */
	public function actionWithQuestions(): ActiveDataProvider
	{
		$searchModel = new QuestionAnswerSearch();

		$searchModel->with_questions = true;

		$dataProvider = $searchModel->search($this->request->get());

		return QuestionAnswerWithQuestionResource::fromDataProvider($dataProvider);
	}

	/**
	 * @throws NotFoundHttpException
	 */
	public function actionView(int $id): QuestionAnswerResource
	{
		return new QuestionAnswerResource($this->findModel($id));
	}

	/**
	 * @throws Exception
	 * @throws ValidateException
	 * @throws SaveModelException
	 */
	public function actionCreate(): QuestionAnswerResource
	{
		$form = new QuestionAnswerForm();

		$form->setScenario(QuestionAnswerForm::SCENARIO_CREATE);

		$form->load($this->request->post());

		$form->validateOrThrow();

		$model = $this->service->create($form->getDto());

		return new QuestionAnswerResource($model);
	}

	/**
	 * @throws Exception
	 * @throws NotFoundHttpException
	 * @throws SaveModelException
	 * @throws ValidateException
	 */
	public function actionUpdate(int $id): QuestionAnswerResource
	{
		$model = $this->findModel($id);

		$form = new QuestionAnswerForm();

		$form->setScenario(QuestionAnswerForm::SCENARIO_UPDATE);

		$form->load($this->request->post());
		$form->validateOrThrow();

		$model = $this->service->update($model, $form->getDto());

		return new QuestionAnswerResource($model);
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
	protected function findModel(int $id): QuestionAnswer
	{
		if (($model = QuestionAnswer::findOne($id)) !== null) {
			return $model;
		}

		throw new NotFoundHttpException('The requested page does not exist.');
	}
}
