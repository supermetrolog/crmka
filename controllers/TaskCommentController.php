<?php

namespace app\controllers;

use app\kernel\common\controller\AppController;
use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\kernel\common\models\exceptions\SaveModelException;
use app\kernel\common\models\exceptions\ValidateException;
use app\models\forms\Task\TaskCommentForm;
use app\models\search\TaskCommentSearch;
use app\models\TaskComment;
use app\resources\Task\TaskCommentResource;
use app\usecases\Task\TaskCommentService;
use yii\data\ActiveDataProvider;

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
		$form->validateOrThrow();

		$model = $this->service->update($model, $form->getDto());

		return new TaskCommentResource($model);
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
}
