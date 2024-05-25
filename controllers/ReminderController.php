<?php

namespace app\controllers;

use app\exceptions\domain\model\SaveModelException;
use app\exceptions\domain\model\ValidateException;
use app\kernel\common\controller\AppController;
use app\kernel\web\http\responses\SuccessResponse;
use app\models\forms\Reminder\ReminderChangeStatusForm;
use app\models\forms\Reminder\ReminderForm;
use app\models\search\ReminderSearch;
use app\models\Reminder;
use app\repositories\ReminderRepository;
use app\resources\ReminderResource;
use app\usecases\Reminder\CreateReminderService;
use app\usecases\Reminder\ReminderService;
use Throwable;
use yii\data\ActiveDataProvider;
use yii\db\StaleObjectException;
use yii\web\NotFoundHttpException;

class ReminderController extends AppController
{
	private ReminderService       $service;
	private CreateReminderService $createReminderService;
	private ReminderRepository    $repository;


	public function __construct(
		$id,
		$module,
		ReminderService $service,
		CreateReminderService $createReminderService,
		ReminderRepository $repository,
		array $config = []
	)
	{
		$this->service            = $service;
		$this->createReminderService = $createReminderService;
		$this->repository         = $repository;

		parent::__construct($id, $module, $config);
	}

	/**
	 * @throws ValidateException
	 */
    public function actionIndex(): ActiveDataProvider
    {
		$searchModel  = new ReminderSearch();
		$dataProvider = $searchModel->search($this->request->get());

		return ReminderResource::fromDataProvider($dataProvider);
    }

	/**
	 * @throws NotFoundHttpException
	 */
    public function actionView(int $id): ReminderResource
    {
		return new ReminderResource($this->findModelByIdAndCreatedBy($id));
    }

	public function actionStatistic(): array
	{
		return $this->repository->getStatusStatisticByUserId($this->request->get('user_id'));
	}

	/**
	 * @throws SaveModelException
	 */
    public function actionCreate(): ReminderResource
	{
		$form = new ReminderForm();

		$form->setScenario(ReminderForm::SCENARIO_CREATE);

		$form->load($this->request->post());

		$form->created_by_id   = $this->user->id;
		$form->created_by_type = $this->user->identity::getMorphClass();

		$form->validateOrThrow();

		$model = $this->createReminderService->create($form->getDto());

		return new ReminderResource($model);
    }

	/**
	 * @return array
	 * @throws ErrorException
	 * @throws SaveModelException
	 * @throws Throwable
	 * @throws ValidateException
	 */
	public function actionCreateForUsers(): array
	{
		$form = new ReminderForm();

		$form->setScenario(ReminderForm::SCENARIO_CREATE_FOR_USERS);

		$form->load($this->request->post());

		$form->created_by_id   = $this->user->id;
		$form->created_by_type = $this->user->identity::getMorphClass();

		$form->validateOrThrow();

		$models = $this->createReminderService->createForUsers($form->getDto());

		return ReminderResource::collection($models);
	}

	/**
	 * @param int $id
	 *
	 * @return ReminderResource
	 * @throws ModelNotFoundException
	 * @throws SaveModelException
	 * @throws ValidateException
	 */
	public function actionUpdate(int $id): ReminderResource
	{
		$model = $this->findModelByIdAndCreatedByOrUserId($id);

		$form = new ReminderForm();

		$form->setScenario(ReminderForm::SCENARIO_UPDATE);

		$form->load($this->request->post());
		$form->validateOrThrow();

		$model = $this->service->update($model, $form->getDto());

		return new ReminderResource($model);
	}

	/**
	 * @param int $id
	 *
	 * @return SuccessResponse
	 * @throws ModelNotFoundException
	 * @throws SaveModelException
	 * @throws ValidateException
	 */
	public function actionChangeStatus(int $id): SuccessResponse
	{
		$reminder = $this->findModelByIdAndCreatedByOrUserId($id);

		$form = new ReminderChangeStatusForm();
		$form->load($this->request->post());

		$form->validateOrThrow();

		$this->service->changeStatus($reminder, $form->status);

		return new SuccessResponse();
	}

	/**
	 * @throws Throwable
	 * @throws StaleObjectException
	 * @throws NotFoundHttpException
	 */
	public function actionDelete(int $id): SuccessResponse
	{
		$this->service->delete($this->findModelByIdAndCreatedBy($id));

		return new SuccessResponse();
	}



	/**
	 * @param int $id
	 *
	 * @throws ModelNotFoundException
	 */
	protected function findModelByIdAndCreatedBy(int $id): Reminder
	{
		return $this->repository->findModelByIdAndCreatedBy($id, $this->user->id, $this->user->identity::getMorphClass());
	}

	/**
	 * @param int $id
	 *
	 * @throws ModelNotFoundException
	 */
	protected function findModelByIdAndCreatedByOrUserId(int $id): Reminder
	{
		return $this->repository->findModelByIdAndCreatedByOrUserId($id, $this->user->id, $this->user->identity::getMorphClass());
	}
}
