<?php

namespace app\controllers;

use app\kernel\common\controller\AppController;
use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\kernel\common\models\exceptions\SaveModelException;
use app\kernel\common\models\exceptions\ValidateException;
use app\models\forms\UserTour\UserTourStatusViewForm;
use app\models\forms\UserTour\UserTourViewForm;
use app\models\search\UserTourViewSearch;
use app\repositories\UserTourStatusRepository;
use app\resources\UserTour\UserTourStatusResource;
use app\resources\UserTour\UserTourViewResource;
use app\usecases\UserTour\UserTourService;
use Throwable;
use yii\base\ErrorException;
use yii\data\ActiveDataProvider;

class UserTourController extends AppController
{
	protected array $viewOnlyAllowedActions = ['*'];

	private UserTourStatusRepository $tourStatusRepository;
	private UserTourService          $userTourService;

	public function __construct(
		$id,
		$module,
		UserTourStatusRepository $tourStatusRepository,
		UserTourService $userTourService,
		array $config = []
	)
	{
		$this->userTourService      = $userTourService;
		$this->tourStatusRepository = $tourStatusRepository;

		parent::__construct($id, $module, $config);
	}

	/**
	 * @return ActiveDataProvider
	 * @throws ErrorException
	 * @throws ValidateException
	 */
	public function actionHistory(): ActiveDataProvider
	{
		$searchModel = new UserTourViewSearch();

		$dataProvider = $searchModel->search($this->request->get());

		return UserTourViewResource::fromDataProvider($dataProvider);
	}

	/**
	 * @throws ValidateException
	 */
	public function actionStatus(): ?UserTourStatusResource
	{
		$form = new UserTourStatusViewForm();

		$form->load($this->request->get());

		$form->validateOrThrow();

		$resource = $this->tourStatusRepository->findOneByUserIdAndTourId($this->user->id, $form->tour_id);

		if (!$resource) {
			return null;
		}

		return new UserTourStatusResource($resource);
	}

	/**
	 * @throws SaveModelException
	 * @throws Throwable
	 * @throws ValidateException
	 */
	public function actionViewed(): UserTourViewResource
	{
		$form = new UserTourViewForm();

		$form->load($this->request->post());

		$form->user_id = $this->user->id;

		$form->validateOrThrow();

		$resource = $this->userTourService->markAsViewed($form->getDto());

		return new UserTourViewResource($resource);
	}

	/**
	 * @throws ModelNotFoundException
	 * @throws SaveModelException
	 */
	public function actionReset(int $id): UserTourStatusResource
	{
		$tourStatus = $this->tourStatusRepository->findOneByIdOrThrow($id);

		$resource = $this->userTourService->reset($tourStatus);

		return new UserTourStatusResource($resource);
	}
}
