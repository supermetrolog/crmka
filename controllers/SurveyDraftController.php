<?php

namespace app\controllers;

use app\kernel\common\controller\AppController;
use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\kernel\common\models\exceptions\SaveModelException;
use app\kernel\common\models\exceptions\ValidateException;
use app\kernel\web\http\responses\SuccessResponse;
use app\models\forms\SurveyDraft\SurveyDraftForm;
use app\models\search\SurveyDraftSearch;
use app\repositories\SurveyDraftRepository;
use app\resources\Survey\SurveyDraftBaseResource;
use app\resources\Survey\SurveyDraftFullResource;
use app\resources\Survey\SurveyDraftShortResource;
use app\usecases\SurveyDraft\SurveyDraftService;
use Exception;
use Throwable;
use yii\base\ErrorException;
use yii\data\ActiveDataProvider;
use yii\db\StaleObjectException;

class SurveyDraftController extends AppController
{
	private SurveyDraftService    $service;
	private SurveyDraftRepository $repository;

	public function __construct(
		$id,
		$module,
		SurveyDraftService $service,
		SurveyDraftRepository $repository,
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
		$searchModel = new SurveyDraftSearch();

		$dataProvider = $searchModel->search($this->request->get());

		return SurveyDraftShortResource::fromDataProvider($dataProvider);
	}

	/**
	 * @throws ModelNotFoundException
	 */
	public function actionView(int $id): SurveyDraftFullResource
	{
		$model = $this->repository->findOneOrThrow($id);

		return new SurveyDraftFullResource($model);
	}

	public function actionViewByChatMember(int $id): ?SurveyDraftFullResource
	{
		$model = $this->repository->findOneByChatMemberIdAndUserId($id, $this->user->id);

		return $model ? new SurveyDraftFullResource($model) : null;
	}

	/**
	 * @throws SaveModelException
	 * @throws ValidateException
	 * @throws Exception
	 */
	public function actionCreate(): SurveyDraftFullResource
	{
		$form = new SurveyDraftForm();

		$form->setScenario(SurveyDraftForm::SCENARIO_CREATE);

		$form->load($this->request->post());

		$form->validateOrThrow();

		$model = $this->service->createOrUpdate($form->getDto());

		return new SurveyDraftFullResource($model);
	}

	/**
	 * @throws ModelNotFoundException
	 * @throws SaveModelException
	 * @throws ValidateException
	 * @throws Exception
	 */
	public function actionUpdate(int $id): SurveyDraftBaseResource
	{
		$model = $this->repository->findOneOrThrow($id);

		$form = new SurveyDraftForm();

		$form->setScenario(SurveyDraftForm::SCENARIO_UPDATE);

		$form->load($this->request->post());
		$form->validateOrThrow();

		$model = $this->service->update($model, $form->getDto());

		return new SurveyDraftBaseResource($model);
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
}
