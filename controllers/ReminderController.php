<?php

namespace app\controllers;

use app\exceptions\domain\model\SaveModelException;
use app\exceptions\domain\model\ValidateException;
use app\kernel\common\controller\AppController;
use app\models\search\ReminderSearch;
use app\models\Reminder;
use Throwable;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\StaleObjectException;
use yii\web\NotFoundHttpException;

class ReminderController extends AppController
{
	/**
	 * @throws ValidateException
	 */
    public function actionIndex(): ActiveDataProvider
    {
        $searchModel = new ReminderSearch();
        return $searchModel->search(Yii::$app->request->queryParams);
    }

	/**
	 * @throws NotFoundHttpException
	 */
    public function actionView(int $id): Reminder
    {
		return $this->findModel($id);
    }

	/**
	 * @throws SaveModelException
	 */
    public function actionCreate(): Reminder    {
        $model = new Reminder();

		$model->load(Yii::$app->request->post());
		$model->saveOrThrow();

		return $model;
    }

	/**
	 * @throws SaveModelException
	 * @throws NotFoundHttpException
	 */
    public function actionUpdate(int $id): Reminder    {
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
    protected function findModel(int $id): ?Reminder
    {
		if (($model = Reminder::findOne($id)) !== null) {
			return $model;
		}

		throw new NotFoundHttpException('The requested page does not exist.');
    }
}
