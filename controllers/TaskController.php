<?php

namespace app\controllers;

use app\dto\task\CreateTaskDto;
use app\dto\task\UpdateTaskDto;
use app\exceptions\domain\model\SaveModelException;
use app\exceptions\domain\model\ValidateException;
use app\kernel\common\controller\AppController;
use app\models\search\TaskSearch;
use app\models\Task;
use app\resources\TaskResource;
use app\usecases\TaskService;
use Throwable;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\StaleObjectException;
use yii\web\NotFoundHttpException;
use yii\web\User;

class TaskController extends AppController
{
	private TaskService $service;

	public function __construct($id, $module, TaskService $service, array $config = [])
	{
		$this->service = $service;

		parent::__construct($id, $module, $config);
	}

	/**
	 * @throws ValidateException
	 */
	public function actionIndex(): ActiveDataProvider
	{
		$searchModel = new TaskSearch();

		return $searchModel->search(Yii::$app->request->queryParams);
	}

	/**
	 * @throws NotFoundHttpException
	 */
	public function actionView(int $id): TaskResource
	{
		return new TaskResource($this->findModel($id));
	}

	/**
	 * @throws SaveModelException
	 * @throws ValidateException
	 */
	public function actionCreate(): TaskResource
	{
		$model = new Task();

		$model->load($this->request->post());
		$model->validateOrThrow();

		$model = $this->service->create(new CreateTaskDto([
			'user'            => $model->user,
			'message'         => $model->message,
			'status'          => Task::STATUS_CREATED,
			'start'           => $model->start,
			'end'             => $model->end,
			'created_by_type' => \app\models\User::tableName(),
			'created_by_id'   => $this->user->id,
		]));

		return new TaskResource($model);
	}

	/**
	 * @throws SaveModelException
	 * @throws NotFoundHttpException
	 */
	public function actionUpdate(int $id): TaskResource
	{
		$model = $this->findModel($id);

		$model->load(Yii::$app->request->post());
		$model->saveOrThrow();

		$model = $this->service->update($model, new UpdateTaskDto([
			'user'    => $model->user,
			'message' => $model->message,
			'status'  => $model->status,
			'start'   => $model->start,
			'end'     => $model->end
		]));


		return new TaskResource($model);
	}

	/**
	 * @throws Throwable
	 * @throws StaleObjectException
	 * @throws NotFoundHttpException
	 */
	public function actionDelete(int $id): void
	{
		$this->service->delete($this->findModel($id));
	}


	/**
	 * @throws NotFoundHttpException
	 */
	protected function findModel(int $id): ?Task
	{
		if (($model = Task::find()->byId($id)->byMorph($this->user->id, \app\models\User::tableName())->one()) !== null) {
			return $model;
		}

		throw new NotFoundHttpException('The requested page does not exist.');
	}
}
