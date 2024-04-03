<?php

namespace app\controllers;

use app\dto\task\CreateTaskDto;
use app\dto\task\UpdateTaskDto;
use app\exceptions\domain\model\SaveModelException;
use app\exceptions\domain\model\ValidateException;
use app\kernel\common\controller\AppController;
use app\models\search\TaskSearch;
use app\models\Task;
use app\models\User;
use app\usecases\TaskService;
use Throwable;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\StaleObjectException;
use yii\web\NotFoundHttpException;

class TaskController extends AppController
{
	private TaskService $service;
	private User        $user;

	public function __construct($id, $module, TaskService $service, array $config = [])
	{
		$this->service = $service;

		$this->user = Yii::$app->user->identity;

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
	public function actionView(int $id): Task
	{
		return $this->findModel($id);
	}

	/**
	 * @throws SaveModelException
	 * @throws ValidateException
	 */
	public function actionCreate(): Task
	{
		$model = new Task();

		$model->load(Yii::$app->request->post());
		$model->validateOrThrow();

		$this->service->create(new CreateTaskDto([
			'user'            => $model->user,
			'message'         => $model->message,
			'status'          => $model->status,
			'start'           => $model->start,
			'end'             => $model->end,
			'created_by_type' => User::class,
			'created_by_id'   => $this->user->id,
		]));

		return $model;
	}

	/**
	 * @throws SaveModelException
	 * @throws NotFoundHttpException
	 */
	public function actionUpdate(int $id): Task
	{
		$model = $this->findModel($id);

		$model->load(Yii::$app->request->post());
		$model->saveOrThrow();

		$this->service->update(new UpdateTaskDto([
			'user'    => $model->user,
			'message' => $model->message,
			'status'  => $model->status,
			'start'   => $model->start,
			'end'     => $model->end
		]));

		return $model;
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
		if (($model = Task::find()->byMorph($id, User::class)->one()) !== null) {
			return $model;
		}

		throw new NotFoundHttpException('The requested page does not exist.');
	}
}
