<?php

namespace app\controllers;

use app\kernel\common\controller\AppController;
use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\kernel\common\models\exceptions\SaveModelException;
use app\kernel\common\models\exceptions\ValidateException;
use app\kernel\web\http\responses\SuccessResponse;
use app\models\forms\Media\MediaForm;
use app\models\forms\Task\TaskCommentForm;
use app\models\Media;
use app\models\search\TaskCommentSearch;
use app\models\TaskComment;
use app\resources\Task\TaskCommentResource;
use app\usecases\Task\TaskCommentService;
use Throwable;
use yii\data\ActiveDataProvider;
use yii\db\StaleObjectException;
use yii\web\UploadedFile;

class TaskCommentController extends AppController
{
	private TaskCommentService $service;


	public function __construct(
		$id,
		$module,
		TaskCommentService $service,
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
		$searchModel  = new TaskCommentSearch();
		$dataProvider = $searchModel->search($this->request->get());

		return TaskCommentResource::fromDataProvider($dataProvider);
	}

	/**
	 * @param int $id
	 *
	 * @return TaskCommentResource
	 * @throws ModelNotFoundException
	 */
	public function actionView(int $id): TaskCommentResource
	{
		return new TaskCommentResource($this->findModelById($id));
	}

	/**
	 * @param int $id
	 *
	 * @return TaskCommentResource
	 * @throws Throwable
	 * @throws ModelNotFoundException
	 * @throws SaveModelException
	 * @throws ValidateException
	 */
	public function actionUpdate(int $id): TaskCommentResource
	{
		$model = $this->findModelById($id);

		$form = new TaskCommentForm();

		$form->setScenario(TaskCommentForm::SCENARIO_UPDATE);

		$form->load($this->request->post());

		$mediaForm = $this->makeMediaForm(Media::CATEGORY_TASK_COMMENT);

		$form->files = $mediaForm->files;
		$form->validateOrThrow();

		$model = $this->service->update($model, $form->getDto(), $mediaForm->getDtos());

		return new TaskCommentResource($model);
	}

	/**
	 * @throws Throwable
	 * @throws ModelNotFoundException
	 * @throws StaleObjectException
	 */
	public function actionDelete(int $id): SuccessResponse
	{
		$task = $this->findModelById($id);

		$this->service->delete($task);

		return new SuccessResponse('Комментарий удален');
	}

	/**
	 * @throws ModelNotFoundException
	 */
	private function findModelById(int $id): TaskComment
	{
		if (($model = TaskComment::findOne($id)) !== null) {
			return $model;
		}

		throw new ModelNotFoundException('TaskComment not found');
	}

	/**
	 * @throws ValidateException
	 */
	private function makeMediaForm(string $category, string $name = 'files'): MediaForm
	{
		$form = new MediaForm();

		$form->category   = $category;
		$form->model_id   = $this->user->id;
		$form->model_type = $this->user->identity::getMorphClass();

		$form->files = UploadedFile::getInstancesByName($name);

		$form->validateOrThrow();

		return $form;
	}
}
