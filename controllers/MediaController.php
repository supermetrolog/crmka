<?php

namespace app\controllers;

use app\kernel\common\controller\AppController;
use app\kernel\common\models\exceptions\ValidateException;
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
