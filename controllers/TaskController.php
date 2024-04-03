<?php

namespace app\controllers;

use app\exceptions\domain\model\SaveModelException;
use app\exceptions\domain\model\ValidateException;
use app\kernel\common\controller\AppController;
use app\models\search\TaskSearch;
use app\models\Task;
use Throwable;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\StaleObjectException;
use yii\web\NotFoundHttpException;

class TaskController extends AppController
{
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
	 */
    public function actionCreate(): Task    {
        $model = new Task();

		$model->load(Yii::$app->request->post());
		$model->saveOrThrow();

		return $model;
    }

	/**
	 * @throws SaveModelException
	 * @throws NotFoundHttpException
	 */
    public function actionUpdate(int $id): Task    {
		$model = $this->findModel($id);

		$model->load(Yii::$app->request->post());
		$model->saveOrThrow();

		return $model;
    }

	/**
	 * @throws Throwable
	 * @throws StaleObjectException
	 * @throws NotFoundHttpException
	 */
    public function actionDelete(int $id): void
    {
		$this->findModel($id)->delete();
    }


	/**
	 * @throws NotFoundHttpException
	 */
    protected function findModel(int $id): ?Task
    {
		if (($model = Task::findOne($id)) !== null) {
			return $model;
		}

		throw new NotFoundHttpException('The requested page does not exist.');
    }
}
