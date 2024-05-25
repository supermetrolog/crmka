<?php

namespace app\controllers;

use app\exceptions\domain\model\SaveModelException;
use app\exceptions\domain\model\ValidateException;
use app\kernel\common\controller\AppController;
use app\models\search\MediaSearch;
use app\models\Media;
use Throwable;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\StaleObjectException;
use yii\web\NotFoundHttpException;

class MediaController extends AppController
{
	/**
	 * @throws ValidateException
	 */
    public function actionIndex(): ActiveDataProvider
    {
        $searchModel = new MediaSearch();
        return $searchModel->search(Yii::$app->request->queryParams);
    }

	/**
	 * @throws NotFoundHttpException
	 */
    public function actionView(int $id): Media
    {
		return $this->findModel($id);
    }

	/**
	 * @throws SaveModelException
	 */
    public function actionCreate(): Media    {
        $model = new Media();

		$model->load(Yii::$app->request->post());
		$model->saveOrThrow();

		return $model;
    }

	/**
	 * @throws SaveModelException
	 * @throws NotFoundHttpException
	 */
    public function actionUpdate(int $id): Media    {
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
    protected function findModel(int $id): ?Media
    {
		if (($model = Media::findOne($id)) !== null) {
			return $model;
		}

		throw new NotFoundHttpException('The requested page does not exist.');
    }
}
