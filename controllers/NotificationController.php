<?php

namespace app\controllers;

use app\kernel\common\controller\AppController;
use app\models\Notification;
use app\models\NotificationSearch;
use Yii;
use yii\data\ActiveDataProvider;

class NotificationController extends AppController
{
	protected array $viewOnlyAllowedActions = ['*'];

	public function actionIndex(): ActiveDataProvider
	{
		$searchModel = new NotificationSearch();

		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		$models     = $dataProvider->getModels();
		$copyModels = Notification::array_copy($models);

		Notification::changeNoViewedStatusToNoCount($models);

		$dataProvider->models = $copyModels;

		return $dataProvider;
	}

	public function actionViewedNotCount($id): void
	{
		Notification::viewedNotCount($id);
	}

	public function actionViewedAll($id): void
	{
		Notification::viewedAll($id);
	}

	public function actionCount($id)
	{
		return Notification::getNotificationsCount($id);
	}
}
