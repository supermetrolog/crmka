<?php

namespace app\controllers;

use app\exceptions\services\TaskFavoriteAlreadyExistsException;
use app\kernel\common\controller\AppController;
use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\kernel\common\models\exceptions\ValidateException;
use app\kernel\web\http\responses\ErrorResponse;
use app\kernel\web\http\responses\SuccessResponse;
use app\models\forms\TaskFavorite\TaskFavoriteChangePositionForm;
use app\models\forms\TaskFavorite\TaskFavoriteForm;
use app\resources\TaskFavoriteResource\TaskFavoriteResource;
use app\usecases\TaskFavorite\TaskFavoriteService;
use Exception;
use Throwable;
use yii\db\StaleObjectException;

class TaskFavoriteController extends AppController
{
	private TaskFavoriteService $service;

	public function __construct(
		$id,
		$module,
		TaskFavoriteService $service,
		array $config = []
	)
	{
		$this->service = $service;

		parent::__construct($id, $module, $config);
	}

	/**
	 * @return TaskFavoriteResource[]
	 */
	public function actionIndex(): array
	{
		$models = $this->service->getAllSortedByUserId($this->user->id);

		return TaskFavoriteResource::collection($models);
	}

	/**
	 * @return TaskFavoriteResource|ErrorResponse
	 *
	 * @throws Exception
	 * @throws Throwable
	 * @throws ValidateException
	 */
	public function actionCreate()
	{
		$form = new TaskFavoriteForm();
		$form->load($this->request->post());

		$form->user_id = $this->user->id;

		$form->validateOrThrow();

		try {
			$model = $this->service->create($form->getDto());

			return new TaskFavoriteResource($model);
		} catch (TaskFavoriteAlreadyExistsException $e) {
			return new ErrorResponse("Задача уже добавлена в избранные.");
		}
	}

	/**
	 * @return ErrorResponse|SuccessResponse
	 *
	 * @throws StaleObjectException
	 * @throws Throwable
	 */
	public function actionDelete(int $id)
	{
		try {
			$model = $this->service->getById($id);

			$this->service->delete($model);

			return new SuccessResponse("Задача была успешна удалена из избранных.");
		} catch (ModelNotFoundException $e) {
			return new ErrorResponse("Задача не является избранной.");
		}
	}

	/**
	 * @throws Throwable
	 * @throws ValidateException
	 */
	public function actionChangePosition(int $id): SuccessResponse
	{
		$form = new TaskFavoriteChangePositionForm();
		$form->load($this->request->post());

		$form->validateOrThrow();

		$this->service->changePosition($id, $form->getDto());

		return new SuccessResponse();
	}
}