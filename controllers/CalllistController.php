<?php

namespace app\controllers;

use app\kernel\common\controller\AppController;
use app\models\CallList;
use app\models\CallListSearch;
use Yii;
use yii\data\ActiveDataProvider;

class CalllistController extends AppController
{
	protected array $viewOnlyAllowedActions = ['*'];

	public function actionIndex(): ActiveDataProvider
	{
		$searchModel = new CallListSearch();

		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		$models     = $dataProvider->getModels();
		$copyModels = CallList::array_copy($models);

		CallList::changeNoViewedStatusToNoCount($models);

		$dataProvider->models = $copyModels;

		return $dataProvider;
	}

	public function actionViewedNotCount($caller_id): void
	{
		CallList::viewedNotCount($caller_id);
	}

	public function actionViewedAll($caller_id): void
	{
		CallList::viewedAll($caller_id);
	}

	public function actionCount($caller_id)
	{
		return CallList::getCallsCount($caller_id);
	}
}
